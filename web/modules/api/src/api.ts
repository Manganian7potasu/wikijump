import Locale from "@wikijump/fluent"
import { get, writable, type Writable } from "svelte/store"
import type { LoginOptions, UserInfo, UserProfile } from "."
import {
  API,
  defaults,
  http,
  HttpError,
  QS,
  _,
  type RequestOptions
} from "../vendor/output"
import type { RegisterOptions, UserIdentity } from "../vendor/types"

const API_PATH = "/api--v0"

interface WikijumpAPIStore {
  authed: boolean
  identity: UserIdentity | null
}

export class WikijumpAPI extends API {
  defaults = defaults
  http = http

  private declare store: Writable<WikijumpAPIStore>
  declare subscribe: WikijumpAPI["store"]["subscribe"]

  /** Current CSRF token. */
  private declare _CSRF?: string

  declare try: {
    [P in keyof WikijumpAPI]: WikijumpAPI[P] extends (
      ...args: infer A
    ) => Promise<infer R>
      ? (...args: A) => Promise<APIResult<R>>
      : never
  }

  constructor() {
    super()
    defaults.baseUrl = API_PATH
    // get most up to date security headers on every request
    Object.defineProperty(defaults, "headers", {
      get: () => this.getSecurityHeaders()
    })

    this.store = writable<WikijumpAPIStore>({ authed: false, identity: null })
    this.subscribe = this.store.subscribe

    // @ts-ignore
    this.try = new Proxy(this, {
      get: (target, name, receiver) => {
        if (name in API.prototype) {
          return (async (...args: any[]) => {
            try {
              // @ts-ignore
              const result = await this[name](...args)
              return new APIResult(result)
            } catch (err: unknown) {
              if (err instanceof HttpError) return new APIResult(err)
              else throw err
            }
          }).bind(this)
        } else {
          Reflect.get(target, name, receiver)
        }
      }
    })

    this.try.authCheck()
  }

  /**
   * Helper for sending a `GET` request via the API.
   *
   * @param to - The path to send the request to.
   * @param query - The query parameters to send, if any.
   */
  async get<T = void>(to: string, query?: Record<string, string>) {
    const url = new URL(to)
    const baseUrl = url.origin
    const path = url.pathname
    return (await _.unwrap(
      query
        ? http.fetchJson(`/${path}${QS.query(QS.form(query))}`, { baseUrl })
        : http.fetchJson(`/${path}`, { baseUrl })
    )) as T
  }

  /**
   * Helper for sending a `POST` request via the API.
   *
   * @param to - The path to send the request to.
   * @param body - The data to send, if any.
   */
  async post<T = void>(to: string, body: any = {}) {
    const url = new URL(to)
    const baseUrl = url.origin
    const path = url.pathname
    return (await _.unwrap(
      http.fetchJson(
        `/${path}`,
        http.json({
          baseUrl,
          method: "POST",
          body
        })
      )
    )) as T
  }

  /** Gets the current security headers. */
  getSecurityHeaders():
    | { "X-CSRF-TOKEN": string }
    | { "X-CSRF-TOKEN": string; "X-XSRF-TOKEN": string } {
    const csrf = this._CSRF ?? getCSRFMeta()
    const xsrf = getCSRFCookie()
    const securityHeaders = xsrf
      ? { "X-CSRF-TOKEN": csrf, "X-XSRF-TOKEN": xsrf }
      : { "X-CSRF-TOKEN": csrf }

    return securityHeaders
  }

  /**
   * Attempts to return the given query parameter from the current URL.
   *
   * @param name - The name of the query parameter to return.
   */
  getQueryParameter(key: string) {
    return new URLSearchParams(window.location.search).get(key)
  }

  /**
   * Attempts to get the specified path segment (via index) from the current URL.
   *
   * @param index - The index of the path segment to return.
   */
  getPathSegment(index: number): string | null {
    return window.location.pathname.split("/")[index + 1] ?? null
  }

  /**
   * Returns a base URL but for a different subdomain.
   *
   * @param subdomain - The subdomain to use.
   */
  subdomainURL(subdomain: string) {
    return `${window.location.protocol}//${subdomain}.${window.location.host}/${API_PATH}`
  }

  /**
   * Formats an error into an error message that can be displayed.
   *
   * @param err - The error to format. While this is typed as `unknown`, it
   *   only really accepts `HttpError` and `APIResult`. Any other type will
   *   rethrow the error.
   */
  formatError(error: unknown) {
    error = error instanceof APIResult ? error.error : error
    if (error instanceof HttpError) {
      console.warn(error, error.data)
      const { status, data } = error
      if (data?.error && Locale.has(`error-api.${data.error}`)) {
        return Locale.format(`error-api.${data.error}`)
      } else if (status >= 500) {
        return Locale.format("error-api.INTERNAL")
      } else {
        return Locale.format("error.api.GENERIC")
      }
    } else {
      throw error
    }
  }

  // -- STORE

  private update(update: Partial<WikijumpAPIStore>) {
    this.store.update(store => ({ ...store, ...update }))
  }

  private async checkIdentity() {
    if (this.authenticated) {
      const res = await this.try.userClientGet()
      if (res.ok && this.identity?.username !== res.result.username) {
        this.update({ identity: res.result })
      } else {
        this.update({ identity: null })
      }
    } else {
      this.update({ identity: null })
    }
  }

  get authenticated() {
    return get(this.store).authed
  }

  get identity() {
    return get(this.store).identity
  }

  // -- OVERRIDES

  override async authLogin(loginOptions: LoginOptions, options?: RequestOptions) {
    const result = await super.authLogin(loginOptions, options)
    this._CSRF = result.csrf
    this.update({ authed: true })
    await this.checkIdentity()
    return result
  }

  override async authLogout(options?: RequestOptions) {
    await super.authLogout(options)
    this.update({ authed: false, identity: null })
  }

  override async authRefresh(options?: RequestOptions) {
    const result = await super.authRefresh(options)
    this._CSRF = result.csrf
    await this.checkIdentity()
    return result
  }

  override async authCheck(options?: RequestOptions) {
    const result = await super.authCheck(options)
    if (result.authed) {
      this.update({ authed: true })
      await this.checkIdentity()
    } else {
      this.update({ authed: false, identity: null })
    }
    return result
  }

  override async accountRegister(
    registerOptions: RegisterOptions,
    options?: RequestOptions
  ) {
    const result = await super.accountRegister(registerOptions, options)
    this._CSRF = result.csrf
    this.update({ authed: true })
    await this.checkIdentity()
    return result
  }

  // -- API EXTENSION

  /**
   * Gets a user via their ID or slug. If the user is not found, returns `null`.
   *
   * @param user - The user to get.
   * @param detail - The detail level to get.
   */
  async getUser(user: number | string): Promise<null | UserIdentity>
  async getUser(user: number | string, detail: "identity"): Promise<null | UserIdentity>
  async getUser(user: number | string, detail: "info"): Promise<null | UserInfo>
  async getUser(user: number | string, detail: "profile"): Promise<null | UserProfile>
  async getUser(
    user: number | string,
    detail: "identity" | "info" | "profile" = "identity"
  ) {
    const pathType = typeof user === "number" ? "id" : "slug"
    const res = await this.try.userGet(pathType, user, { detail })
    return res.ok ? res.result : null
  }

  /**
   * Gets the current user's identity. If the user is not logged in, returns `null`.
   *
   * @param detail - The detail level to get.
   */
  async getClient(): Promise<null | UserIdentity>
  async getClient(detail: "identity"): Promise<null | UserIdentity>
  async getClient(detail: "info"): Promise<null | UserInfo>
  async getClient(detail: "profile"): Promise<null | UserProfile>
  async getClient(detail: "identity" | "info" | "profile" = "identity") {
    const res = await this.try.userClientGet({ detail })
    return res.ok ? res.result : null
  }
}

export class APIResult<T> {
  private declare _error?: HttpError
  private declare _result?: T

  declare readonly ok: boolean

  constructor(result: T | HttpError) {
    if (result instanceof HttpError) {
      this._error = result
      this.ok = false
    } else {
      this._result = result
      this.ok = true
    }
  }

  get error() {
    if (this.ok) throw new Error("Result is not an error")
    return this._error!
  }

  get result() {
    if (!this.ok) throw new Error("Result is not ok")
    return this._result!
  }

  unwrapOrNull() {
    return this.ok ? this.result : null
  }
}

export default new WikijumpAPI()

/**
 * Retrieves the CSRF token from the `<meta name="csrf-token" ...>` tag in
 * the `<head>`. This should always be present, so this function throws if
 * that element can't be found.
 */
function getCSRFMeta() {
  const meta = document.head.querySelector("meta[name=csrf-token]")
  if (!meta) throw new Error("No CSRF meta tag found")
  return meta.getAttribute("content")!
}

/** Retrieves the CSRF token from the `XSRF-TOKEN` cookie, if it exists. */
function getCSRFCookie() {
  const value = document.cookie
    .split(/;\s*/)
    .find(c => c.startsWith("XSRF-TOKEN="))
    ?.split("=")[1]
  return value
}
