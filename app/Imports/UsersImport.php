<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    /**
     * @var array
     */
    protected array $result = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => [],
    ];

    /** @var bool */
    protected bool $updateExisting;

    /** @var bool */
    protected bool $dryRun = false;

    /** @var array */
    protected array $allowedModules = [];

    /** @var array */
    protected array $allowedLevels = [
        'admin','ceo','cfo','manager','spv','staff','headstore','kasir','sales','mekanik','pr_user'
    ];

    public function __construct(bool $updateExisting = true, bool $dryRun = false, array $allowedModules = [])
    {
        $this->updateExisting = $updateExisting;
        $this->dryRun = $dryRun;
        $this->allowedModules = $allowedModules;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $email = strtolower(trim((string)($row['email'] ?? '')));
                $name = trim((string)($row['name'] ?? ''));
                $systemAccessRaw = $row['system_access'] ?? null;
                
                // Parse system access first to validate it
                $systemAccess = $this->parseSystemAccess($systemAccessRaw);
                
                // Required field validations - hanya name, email, dan system_access yang wajib
                if (!$email || !$name) {
                    $this->result['skipped']++;
                    $this->result['errors'][] = "Row ".($index+2).": Name dan Email wajib diisi";
                    continue;
                }
                
                if ($systemAccess === null || empty($systemAccess)) {
                    $this->result['skipped']++;
                    $this->result['errors'][] = "Row ".($index+2).": System Access wajib diisi";
                    continue;
                }

                // Optional fields - jika tidak diisi, gunakan default atau null
                $level = strtolower(trim((string)($row['level'] ?? 'staff'))); // default staff
                $divisi = !empty($row['divisi']) ? trim((string)$row['divisi']) : null;
                $garage = !empty($row['garage']) ? trim((string)$row['garage']) : null;
                $passwordRaw = !empty($row['password']) ? $row['password'] : null;

                // Validations untuk optional fields
                if (!in_array($level, $this->allowedLevels, true)) {
                    $this->result['skipped']++;
                    $this->result['errors'][] = "Row ".($index+2).": Level '{$level}' tidak valid. Level yang tersedia: " . implode(', ', $this->allowedLevels);
                    continue;
                }
                
                // System access validation
                if (!empty($this->allowedModules)) {
                    $invalid = array_diff($systemAccess, $this->allowedModules);
                    if (count($invalid) > 0) {
                        $this->result['skipped']++;
                        $this->result['errors'][] = "Row ".($index+2).": System access tidak valid: " . implode(', ', $invalid);
                        continue;
                    }
                }

                $user = User::where('email', $email)->first();

                if ($user) {
                    if (!$this->updateExisting) {
                        $this->result['skipped']++;
                        continue;
                    }

                    // Data yang selalu diupdate
                    $updateData = [
                        'name' => $name,
                        'level' => $level,
                        'system_access' => $systemAccess,
                    ];

                    // Tambahkan field opsional hanya jika ada nilainya
                    if ($divisi !== null) {
                        $updateData['divisi'] = $divisi;
                    }
                    if ($garage !== null) {
                        $updateData['garage'] = $garage;
                    }

                    // Update password hanya jika disediakan
                    if ($passwordRaw !== null && $passwordRaw !== '') {
                        $updateData['password'] = Hash::make((string)$passwordRaw);
                    }

                    if (!$this->dryRun) {
                        $user->update($updateData);
                    }
                    $this->result['updated']++;
                } else {
                    // Generate password jika tidak disediakan
                    $passwordToUse = $passwordRaw && $passwordRaw !== ''
                        ? (string)$passwordRaw
                        : Str::random(10);

                    // Data untuk create user
                    $userData = [
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($passwordToUse),
                        'level' => $level,
                        'system_access' => $systemAccess,
                    ];
                    
                    // Tambahkan field opsional hanya jika ada nilainya
                    if ($divisi !== null) {
                        $userData['divisi'] = $divisi;
                    }
                    if ($garage !== null) {
                        $userData['garage'] = $garage;
                    }

                    if (!$this->dryRun) {
                        User::create($userData);
                    }
                    $this->result['created']++;
                }
            } catch (\Throwable $e) {
                $this->result['errors'][] = "Row ".($index+2).": ".$e->getMessage();
                $this->result['skipped']++;
            }
        }
    }

    /**
     * Parse system access value from sheet cell.
     * Accepts comma-separated ("a,b,c") or JSON array string ("[\"a\",\"b\"]").
     * Returns array or null when not provided.
     */
    protected function parseSystemAccess($value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return array_values(array_unique(array_filter(array_map(fn($v) => trim((string)$v), $value))));
        }

        $str = trim((string)$value);
        // Try JSON decode
    if ((str_starts_with($str, '[') && str_ends_with($str, ']')) || (str_starts_with($str, '"['))) {
            $decoded = json_decode($str, true);
            if (is_array($decoded)) {
                return array_values(array_unique(array_filter(array_map(fn($v) => trim((string)$v), $decoded))));
            }
        }

        // Fallback: comma separated
        $parts = array_map('trim', explode(',', $str));
        $parts = array_values(array_unique(array_filter($parts)));
        return $parts;
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
