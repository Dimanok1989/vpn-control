<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IpsecApiController extends Controller
{
    public function status()
    {
        exec('sudo ipsec statusall', $statusall, $code);

        if (!empty($code)) {
            abort(403);
        }

        exec('sudo ipsec leases', $leases);

        $leases = collect($leases)
            ->filter(
                fn ($item, $key) => $key > 0
                    && strripos($item, "no matching leases found") === false
            )
            ->map(function ($item) {

                $array = explode(" ", trim($item));
                $item = collect($array)->filter()->values()->all();

                return [
                    'ip' => $item[0] ?? null,
                    'status' => $item[1] ?? null,
                    'username' => str_replace("'", "", $item[2] ?? ""),
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'statusall' => $statusall,
            'leases' => $leases,
        ]);
    }
}
