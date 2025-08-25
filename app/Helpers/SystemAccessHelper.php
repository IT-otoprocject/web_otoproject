<?php

if (!function_exists('user_has_access')) {
    /**
     * Check if current authenticated user has access to a module
     *
     * @param string $module
     * @return bool
     */
    function user_has_access($module)
    {
        $user = auth()->user();
        return $user ? $user->hasAccess($module) : false;
    }
}

if (!function_exists('user_has_any_access')) {
    /**
     * Check if current authenticated user has any of the specified accesses
     *
     * @param array $modules
     * @return bool
     */
    function user_has_any_access(array $modules)
    {
        $user = auth()->user();
        return $user ? $user->hasAnyAccess($modules) : false;
    }
}

if (!function_exists('user_accessible_modules')) {
    /**
     * Get current authenticated user's accessible modules
     *
     * @return array
     */
    function user_accessible_modules()
    {
        $user = auth()->user();
        return $user ? $user->getAccessibleModules() : [];
    }
}

if (!function_exists('can_access_spk')) {
    /**
     * Check if current user can access SPK
     *
     * @return bool
     */
    function can_access_spk()
    {
        return user_has_access('spk_garage');
    }
}

if (!function_exists('can_access_pr')) {
    /**
     * Check if current user can access PR
     *
     * @return bool
     */
    function can_access_pr()
    {
        return user_has_access('pr');
    }
}
