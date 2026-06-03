<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait MasksSensitiveData
{
    /**
     * Boot the trait to prevent saving masked strings.
     */
    public static function bootMasksSensitiveData()
    {
        static::saving(function ($model) {
            if (! property_exists($model, 'maskable')) {
                return;
            }

            // Prevent direct attributes from being saved as asterisks
            $attributes = $model->getAttributes();
            foreach ($model->maskable as $key) {
                if (array_key_exists($key, $attributes) && is_string($attributes[$key]) && str_contains($attributes[$key], '*')) {
                    $original = $model->getOriginal($key);
                    if ($original !== null) {
                        $model->$key = $original;
                    } else {
                        unset($model->$key);
                    }
                }
            }

            // Prevent JSON form_data attributes from being saved as asterisks
            if (isset($model->form_data) && is_array($model->form_data)) {
                $formData = $model->form_data;
                $originalFormData = $model->getOriginal('form_data');
                if (is_string($originalFormData)) {
                    $originalFormData = json_decode($originalFormData, true);
                }

                $modified = false;
                foreach ($model->maskable as $key) {
                    if (isset($formData[$key]) && is_string($formData[$key]) && str_contains($formData[$key], '*')) {
                        if (is_array($originalFormData) && isset($originalFormData[$key])) {
                            $formData[$key] = $originalFormData[$key];
                            $modified = true;
                        }
                    }
                }

                if ($modified) {
                    $model->form_data = $formData;
                }
            }
        });
    }

    /**
     * Override Eloquent's getAttributeValue to intercept and mask sensitive data.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        // 1. Get the original value from the parent Model's normal resolution
        $value = parent::getAttributeValue($key);

        // 2. Intercept and mask ONLY if conditions are met
        if (is_string($value) && $this->isMaskable($key) && $this->shouldMaskForCurrentUser()) {
            return $this->applyMask($key, $value);
        }

        return $value;
    }

    /**
     * Override Eloquent's toArray to intercept JSON serialization (e.g. Yajra DataTables).
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        if ($this->shouldMaskForCurrentUser()) {
            foreach ($this->maskable as $key) {
                if (array_key_exists($key, $array) && is_string($array[$key])) {
                    $array[$key] = $this->applyMask($key, $array[$key]);
                }
            }
        }

        return $array;
    }

    /**
     * Determine if the given attribute is registered for masking.
     */
    protected function isMaskable(string $key): bool
    {
        return property_exists($this, 'maskable') && in_array($key, $this->maskable);
    }

    /**
     * Determine if the current user should see masked data.
     *
     * Applies ONLY to the 'sub-admin' role.
     * Admin, background jobs, and console commands will receive raw data.
     */
    protected function shouldMaskForCurrentUser(): bool
    {
        // Fail-safe: Never mask during console commands, background jobs, or migrations
        if (app()->runningInConsole()) {
            return false;
        }

        $user = Auth::user();

        // If no user is authenticated, or the user's role is not exactly 'sub-admin', do not mask
        if (! $user || strtolower($user->role) !== 'sub-admin') {
            return false;
        }

        return true;
    }

    /**
     * Route the value to the appropriate masking algorithm.
     */
    protected function applyMask(string $key, string $value): string
    {
        if (empty($value)) {
            return $value;
        }

        $normalizedKey = strtolower($key);

        if (str_contains($normalizedKey, 'email')) {
            return $this->maskEmail($value);
        }

        if (str_contains($normalizedKey, 'phone') || str_contains($normalizedKey, 'mobile') || str_contains($normalizedKey, 'whatsapp')) {
            return $this->maskPhone($value);
        }

        return $this->maskDefault($value);
    }

    /**
     * Mask email: Show first letter, mask middle, show domain (e.g., j****@gmail.com).
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);

        // Fallback to default mask if it's an invalid email string missing the @ symbol
        if (count($parts) !== 2) {
            return $this->maskDefault($email);
        }

        $username = $parts[0];
        $domain = $parts[1];

        $firstChar = mb_substr($username, 0, 1);
        // Ensure at least one asterisk is shown even for 1-character usernames
        $maskedUsername = $firstChar.str_repeat('*', max(1, mb_strlen($username) - 1));

        return $maskedUsername.'@'.$domain;
    }

    /**
     * Mask phone: Mask all but the last 4 characters.
     */
    protected function maskPhone(string $phone): string
    {
        // For standard phone formatting, utilizing the default logic handles last 4 digits well.
        return $this->maskDefault($phone);
    }

    /**
     * Default string mask: Mask all but the last 4 characters.
     */
    protected function maskDefault(string $string): string
    {
        $visibleLength = 4;
        $length = mb_strlen($string);

        // If the string is too short to leave 4 visible characters, mask it entirely
        if ($length <= $visibleLength) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - $visibleLength).mb_substr($string, -$visibleLength);
    }
}
