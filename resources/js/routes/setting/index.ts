import account from './account'
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