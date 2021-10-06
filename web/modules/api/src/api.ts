import { Api } from "../vendor/api"

export class WikijumpAPI extends Api<void> {
  // TODO: allow giving a specific site here
  constructor(baseUrl = "/api--v1") {
    super({ baseUrl, baseApiParams: { format: "json" } })
  }
}