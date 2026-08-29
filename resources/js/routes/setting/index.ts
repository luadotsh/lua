import account from './account'
import authentication from './authentication'
import workspace from './workspace'
import domains from './domains'
import billing from './billing'
import teamMembers from './team-members'
import invites from './invites'
import tags from './tags'
import usage from './usage'
import apiTokens from './api-tokens'

const setting = {
    account: Object.assign(account, account),
    authentication: Object.assign(authentication, authentication),
    workspace: Object.assign(workspace, workspace),
    domains: Object.assign(domains, domains),
    billing: Object.assign(billing, billing),
    teamMembers: Object.assign(teamMembers, teamMembers),
    invites: Object.assign(invites, invites),
    tags: Object.assign(tags, tags),
    usage: Object.assign(usage, usage),
    apiTokens: Object.assign(apiTokens, apiTokens),
}

export default setting