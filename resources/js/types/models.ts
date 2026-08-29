// Mirrors App\Enums\Domain\Status
export type DomainStatus = 'active' | 'pending';

// Mirrors App\Enums\Tag\Color
export type TagColor =
    | 'red'
    | 'orange'
    | 'yellow'
    | 'green'
    | 'cyan'
    | 'teal'
    | 'blue'
    | 'indigo'
    | 'purple'
    | 'fuchsia'
    | 'pink'
    | 'zinc';

// A Passport personal access token, as shipped by ApiTokenController.
// The token value itself is only ever returned once, at creation.
export type ApiToken = {
    id: string;
    name: string | null;
    last_used_at: string | null;
    expires_at: string | null;
    created_at: string | null;
};

// An OAuth grant an MCP client currently holds against this workspace.
export type McpClient = {
    id: string;
    name: string | null;
    last_used_at: string | null;
    created_at: string | null;
};

// Mirrors App\Models\Domain
export type Domain = {
    id: string | number;
    workspace_id: string;
    domain: string;
    status: DomainStatus;
    not_found_url: string | null;
    expired_url: string | null;
    created_at: string;
    updated_at: string;
};

// Mirrors App\Models\Tag
export type Tag = {
    id: string | number;
    workspace_id: string;
    name: string;
    sort: number;
    color: TagColor;
    created_at: string;
    updated_at: string;
};
