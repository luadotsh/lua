// Mirrors App\Enums\User\Role. Ownership is not a role — it is
// workspaces.owner_id, and arrives as `is_owner` on the current workspace.
export const WorkspaceRole = {
    Admin: 'ADMIN',
    User: 'USER',
} as const;

export type WorkspaceRole = (typeof WorkspaceRole)[keyof typeof WorkspaceRole];

// Mirrors App\Models\Media.
export type Media = {
    id: string;
    collection: string;
    url: string;
    original_filename: string;
    mime_type: string;
    size: number;
};

export type User = {
    id: string;
    name: string;
    email: string;
    has_photo: boolean;
    photo_url: string | null;
    email_verified_at: string | null;
    current_workspace_id: string | null;
    current_workspace: Workspace | null;
    workspaces: Pick<Workspace, 'id' | 'name' | 'has_logo' | 'logo_url'>[];
    media?: Media[];
    created_at: string;
    updated_at: string;
};

export type WorkspacePlan = {
    id: string;
    slug: string;
    name: string;
    link_limit: number;
    event_limit: number;
    domain_limit: number;
    member_limit: number;
    is_free: boolean;
};

export type Workspace = {
    id: string;
    name: string;
    has_logo: boolean;
    logo_url: string | null;
    created_at: string;
    plan: WorkspacePlan | null;
    subscribed: boolean;
    role: WorkspaceRole | null;
    /** Ownership is a column, not a role, so it travels alongside one. */
    is_owner?: boolean;
    media?: Media[];
};

export type Auth = {
    user: User;
};

export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';
