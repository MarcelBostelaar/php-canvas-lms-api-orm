Below is a clean, practical caching strategy for your scenario — one where multiple permission sources can retrieve the same items, and you want to re-use cached results for any client whose permissions are a subset of the permissions used to generate that result.

This is the “permission-set caching” scheme with dominance (subset) lookup.

✅ The Goal

If a cached set was fetched using permission set:

{sourceA, sourceB}


then any client whose permissions satisfy:

userPerms ⊆ {sourceA, sourceB}


should be allowed to re-use this cached result (filtered, if needed).

✔ The Structure You Need

Keep a cache keyed by the exact permission set used to produce it, e.g.:

cache[
   ["sourceA"]                → ResultSet A
   ["sourceB"]                → ResultSet B
   ["sourceA","sourceB"]      → ResultSet AB
   ["sourceA","sourceC","X"]  → ResultSet ACX
   ...
]


Normalize keys by sorting and hashing them:

key = sha1(json_encode(sorted(permissionList)))


Each cache entry stores:

class CachedResult {
    public array $permissions;   // the exact permissions used for fetching
    public array $items;         // the full result set returned by that fetch
}

✔ Lookup Logic (Dominance Matching)

When a new user requests with permissions P_user:

1. Find any cached result whose permission set dominates the user set

In set terms:

P_user ⊆ P_cache


In code:

function isSubset(array $subset, array $set): bool {
    return !array_diff($subset, $set);
}

2. Prefer the smallest dominating cache

You don't want to use a huge permission set if a smaller one works.

Algorithm:

$candidates = [];

foreach ($cache as $cachedKey => $result) {
    if (isSubset($userPermissions, $result->permissions)) {
        $candidates[] = $result;
    }
}

// Pick the cached set with the smallest permission set
usort($candidates, fn($a, $b) =>
    count($a->permissions) <=> count($b->permissions)
);

$best = $candidates[0] ?? null;

3. If a dominating candidate exists:

Return filterItemsByUserPermissions($best->items)

Do not refetch

4. If no candidate dominates:

Fetch fresh from the API using the user's permissions

Write new cache entry under this permission set