<?php

declare(strict_types=1);

namespace App\Enums\User;

/**
 * What a member may do inside a workspace.
 *
 * Ownership is not here: it is workspaces.owner_id, because it belongs to one
 * person and a column cannot end up holding two of them, or none.
 */
enum Role: string
{
    /** Runs the workspace: members, invites, settings and API keys. */
    case ROLE_ADMIN = 'ADMIN';

    /** Does the work: links, tags and domains. */
    case ROLE_USER = 'USER';
}
