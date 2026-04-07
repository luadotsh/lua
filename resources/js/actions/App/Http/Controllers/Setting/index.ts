import AccountController from './AccountController'
import WorkspaceController from './WorkspaceController'
import DomainController from './DomainController'
import BillingController from './BillingController'
import TeamMemberController from './TeamMemberController'
import InviteController from './InviteController'
import TagController from './TagController'
import UsageController from './UsageController'
import ApiTokenController from './ApiTokenController'

const Setting = {
    AccountController: Object.assign(AccountController, AccountController),
    WorkspaceController: Object.assign(WorkspaceController, WorkspaceController),
    DomainController: Object.assign(DomainController, DomainController),
    BillingController: Object.assign(BillingController, BillingController),
    TeamMemberController: Object.assign(TeamMemberController, TeamMemberController),
    InviteController: Object.assign(InviteController, InviteController),
    TagController: Object.assign(TagController, TagController),
    UsageController: Object.assign(UsageController, UsageController),
    ApiTokenController: Object.assign(ApiTokenController, ApiTokenController),
}

export default Setting