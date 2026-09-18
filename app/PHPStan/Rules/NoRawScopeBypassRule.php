<?php

namespace App\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Menegakkan checklist Tahap 6: withoutGlobalScope()/withoutGlobalScopes()
 * hanya boleh dipakai di dalam PublicCatalogQuery. Bypass yang tersebar di
 * controller/service lain praktis mustahil diaudit, jadi ditolak di sini
 * sebelum sempat masuk ke code review.
 *
 * @implements Rule<MethodCall>
 */
final class NoRawScopeBypassRule implements Rule
{
    private const BANNED_METHODS = [
        'withoutGlobalScope',
        'withoutGlobalScopes',
    ];

    /**
     * File yang diizinkan memanggil method di atas.
     * Gunakan akhiran path relatif agar tidak tergantung drive/OS.
     */
    private const ALLOWED_FILE_SUFFIX = 'app/Modules/Catalog/Services/PublicCatalogQuery.php';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param  MethodCall  $node
     * @return list<RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Node\Identifier) {
            return [];
        }

        $methodName = $node->name->toString();

        if (! in_array($methodName, self::BANNED_METHODS, true)) {
            return [];
        }

        $file = str_replace('\\', '/', $scope->getFile());

        if (str_ends_with($file, self::ALLOWED_FILE_SUFFIX)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                'Pemanggilan %s() hanya diizinkan di dalam %s. '
                . 'Bypass global scope tenant di luar kelas itu melanggar '
                . 'kebijakan isolasi tenant (Modul 4, Tahap 6) dan sulit diaudit. '
                . 'Pindahkan query ini ke PublicCatalogQuery, atau tambahkan '
                . 'method baru di sana.',
                $methodName,
                self::ALLOWED_FILE_SUFFIX
            ))->identifier('tenancy.rawScopeBypass')->build(),
        ];
    }
}
