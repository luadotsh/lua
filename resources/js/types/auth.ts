export const WorkspaceRole = {
    Admin: 'admin',
    Member: 'member',
} as const;

export type WorkspaceRole = (typeof WorkspaceRole)[keyof typeof WorkspaceRole];

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
};

export type Auth = {
    user: User;
};

export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';
