<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class AgentLineageService
{
    /**
     * Assign an agent to a parent and compute their materialized ancestry path and depth.
     */
    public static function assignParent(User $agent, User $parent): void
    {
        if (self::wouldCreateCycle($agent, $parent)) {
            throw new InvalidArgumentException("Cannot assign agent #{$parent->id} as parent to agent #{$agent->id} (would create circular hierarchy).");
        }

        // Ensure parent has a valid ancestry path
        if (empty($parent->ancestry_path)) {
            self::ensureAncestryPath($parent);
        }

        $agent->parent_id = $parent->id;
        $agent->ancestry_path = rtrim($parent->ancestry_path, '/').'/'.$agent->id.'/';
        $agent->depth = ((int) ($parent->depth ?: 1)) + 1;
        $agent->save();
    }

    /**
     * Mark an agent as a root master agent.
     */
    public static function assignRoot(User $agent): void
    {
        $agent->parent_id = null;
        $agent->ancestry_path = "/{$agent->id}/";
        $agent->depth = 1;
        $agent->save();
    }

    /**
     * Ensure that an agent has an ancestry path and depth populated.
     */
    public static function ensureAncestryPath(User $agent): void
    {
        if (! empty($agent->ancestry_path) && ! empty($agent->depth)) {
            return;
        }

        if (empty($agent->parent_id)) {
            $agent->ancestry_path = "/{$agent->id}/";
            $agent->depth = 1;
        } else {
            $parent = $agent->parentAgent ?? User::find($agent->parent_id);
            if ($parent) {
                self::ensureAncestryPath($parent);
                $agent->ancestry_path = rtrim($parent->ancestry_path, '/').'/'.$agent->id.'/';
                $agent->depth = ((int) ($parent->depth ?: 1)) + 1;
            } else {
                $agent->ancestry_path = "/{$agent->id}/";
                $agent->depth = 1;
            }
        }

        $agent->save();
    }

    /**
     * Check if assigning $proposedParent to $agent would cause a cyclic loop.
     */
    public static function wouldCreateCycle(User $agent, User $proposedParent): bool
    {
        if ($agent->id === $proposedParent->id) {
            return true;
        }

        if (! empty($proposedParent->ancestry_path) && str_contains($proposedParent->ancestry_path, "/{$agent->id}/")) {
            return true;
        }

        return false;
    }

    /**
     * Get array of ancestor IDs ordered from root master down to direct parent.
     *
     * @return array<int>
     */
    public static function getAncestorIds(User $agent): array
    {
        self::ensureAncestryPath($agent);

        $parts = array_filter(explode('/', trim($agent->ancestry_path, '/')));
        array_pop($parts); // Remove agent's own ID

        return array_map('intval', $parts);
    }

    /**
     * Get all upline ancestors ordered from direct parent up to root master.
     *
     * @return Collection<int, User>
     */
    public static function getAncestors(User $agent): Collection
    {
        $ancestorIds = self::getAncestorIds($agent);
        if (empty($ancestorIds)) {
            return collect();
        }

        $users = User::whereIn('id', $ancestorIds)->get()->keyBy('id');

        // Return ordered from direct parent (closest) up to root
        $ordered = collect();
        foreach (array_reverse($ancestorIds) as $id) {
            if (isset($users[$id])) {
                $ordered->push($users[$id]);
            }
        }

        return $ordered;
    }

    /**
     * Get the root master agent at the very top of this agent's agency tree.
     */
    public static function getRootAgent(User $agent): User
    {
        self::ensureAncestryPath($agent);

        $ancestorIds = self::getAncestorIds($agent);
        if (empty($ancestorIds)) {
            return $agent;
        }

        $rootId = reset($ancestorIds);

        return User::find($rootId) ?? $agent;
    }

    /**
     * Get Eloquent query for all descendants (children, grandchildren, etc.).
     */
    public static function getDescendantsQuery(User $agent, ?int $maxDepth = null): Builder
    {
        self::ensureAncestryPath($agent);

        $pathPrefix = $agent->ancestry_path;

        $query = User::query()
            ->where('ancestry_path', 'like', $pathPrefix.'%')
            ->where('id', '!=', $agent->id);

        if ($maxDepth !== null) {
            $query->where('depth', '<=', $agent->depth + $maxDepth);
        }

        return $query;
    }

    /**
     * Get array of all descendant IDs across all downline tiers.
     *
     * @return array<int>
     */
    public static function getDescendantIds(User $agent, ?int $maxDepth = null): array
    {
        return self::getDescendantsQuery($agent, $maxDepth)->pluck('id')->toArray();
    }
}
