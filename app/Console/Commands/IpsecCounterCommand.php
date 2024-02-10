<?php

namespace App\Console\Commands;

use App\Models\IpsecConnection;
use Illuminate\Console\Command;

class IpsecCounterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ipsec-counter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Счетчик времени и трафика онлайн пользователей';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        exec('sudo ipsec statusall', $statusall, $code);

        if (!empty($code)) {
            $this->error('Exit code ' . $code);
            return $code;
        }

        $groupId = null;

        collect($statusall)
            ->map(function ($str) {

                $pattern = "/ikev2-vpn\[\d+\]|ikev2-vpn\{\d+\}/u";
                preg_match_all($pattern, $str, $matches);

                if (!empty($matches[0])) {
                    return trim($str);
                }
            })
            ->filter()
            ->values()
            ->map(function ($string) use (&$groupId) {

                $pattern = "/\[(\d+)\]/";
                preg_match($pattern, $string, $matches);
                $number = $matches[1] ?? $groupId;
                $groupId = $number;

                return [
                    'group' => $number,
                    'item' => $string,
                ];
            })
            ->groupBy('group')
            ->map(function ($items, $groupId) {

                $query = $items->pluck('item')
                    ->map(function ($item) {

                        if (strpos($item, "ESTABLISHED") !== false) {
                            $items = explode("ESTABLISHED", $item);
                            $times = explode(", ", $items[1] ?? "");
                            return "time=" . trim($times[0] ?? "");
                        } else if (strpos($item, "Remote EAP identity: ") !== false) {
                            $items = explode("Remote EAP identity: ", $item);
                            return "username=" . trim($items[1] ?? "");
                        } else if (strpos($item, ", reqid ") !== false) {
                            $pattern = "/reqid (\d+)/";
                            preg_match($pattern, $item, $matches);
                            return "reqid=" . ($matches[1] ?? "");
                        } else if (strpos($item, "bytes_i") !== false || strpos($item, "bytes_o") !== false) {
                            $pattern = "/(\d+) bytes_i .* (\d+) bytes_o/";
                            preg_match($pattern, $item, $matches);
                            return "bytes_i=" . ($matches[1] ?? "")
                                . "&bytes_o=" . ($matches[2] ?? "");
                        }
                    })
                    ->filter()
                    ->values()
                    ->join("&");

                parse_str($query, $params);

                return [
                    'uniqueid' => $groupId,
                    ...$params,
                ];
            })
            ->values()
            ->each(function ($item) {

                $connect = IpsecConnection::where('uniqueid', $item['uniqueid'])
                    ->when(!empty($item['reqid']), function ($query) use ($item) {
                        $query->where('reqid', $item['reqid']);
                    })
                    ->where('verb', 'up-client')
                    ->orderByDesc('id')
                    ->first();

                if (!$connect) {
                    return;
                }

                $connect->update([
                    'bytes_in' => $item['bytes_i'] ?? $connect->bytes_in,
                    'bytes_out' => $item['bytes_o'] ?? $connect->bytes_out,
                ]);
            })
            ->dump();
    }
}
