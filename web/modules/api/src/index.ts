import { WikijumpAPI } from "./api"

export default WikijumpAPI

export { ContentType, ForumSortingTypes, ReferenceTypes } from "../vendor/api"
// there really isn't a decent way to just "pick out" exports,
// so this type export is a bit manual, but oh well
export type {
  AccountSettings,
  AccountSettingsPatch,
  ApiConfig,
  Application,
  ApplicationList,
  ApplicationSend,
  ApplicationSendList,
  Base64,
  CastVote,
  CastVotePlus,
  CastVotePlusMinus,
  CastVoteStar,
  Category,
  CategoryDefault,
  CategoryDefaultPatch,
  CategoryList,
  CategoryPatch,
  CreateSiteSettings,
  Email,
  FileData,
  FileMetadata,
  FileSiteMetadata,
  FileUpload,
  Forum,
  ForumCategory,
  ForumCategoryList,
  ForumCreationContext,
  ForumGroup,
  ForumGroupList,
  ForumPost,
  ForumPostList,
  ForumThread,
  ForumThreadList,
  FTMLSyntaxTree,
  FullRequestParams,
  HTML,
  HTMLObj,
  Invite,
  InviteList,
  InviteSend,
  LoginSpecifier,
  Membership,
  MembershipList,
  MembershipRole,
  Message,
  MessageList,
  MessageSend,
  Mime,
  Notification,
  NotificationList,
  Page,
  Paginated,
  Reference,
  Report,
  ReportList,
  ReportSend,
  RequestParams,
  Revision,
  RevisionHistory,
  Score,
  SiteName,
  SiteNewsletter,
  SiteSettings,
  SiteSettingsPatch,
  SiteTransfer,
  Slug,
  TagList,
  UserBlockedList,
  UserIdentity,
  UserInfo,
  Username,
  UserProfile,
  UserProfilePatch,
  UserRole,
  Vote,
  VoterList,
  Wikitext,
  WikitextObj
} from "../vendor/api"
export * from "./api"
export * from "./asset"
