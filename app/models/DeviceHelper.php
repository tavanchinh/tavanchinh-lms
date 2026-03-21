<?php 
class DeviceHelper {
    public static function parseUserAgent($ua) {
        $os = "Unknown OS";
        $browser = "Unknown Browser";
        $device = "PC";

        // 1. Lấy Hệ điều hành
        if (preg_match('/iphone/i', $ua)) { $os = "iOS (iPhone)"; $device = "Mobile"; }
        elseif (preg_match('/android/i', $ua)) { $os = "Android"; $device = "Mobile"; }
        elseif (preg_match('/windows/i', $ua)) { $os = "Windows"; }
        elseif (preg_match('/macintosh|mac os x/i', $ua)) { $os = "Mac OS"; }

        // 2. Lấy Trình duyệt
        if (preg_match('/chrome/i', $ua)) { $browser = "Chrome"; }
        elseif (preg_match('/safari/i', $ua)) { $browser = "Safari"; }
        elseif (preg_match('/firefox/i', $ua)) { $browser = "Firefox"; }
        elseif (preg_match('/edge/i', $ua)) { $browser = "Edge"; }

        return "$device - $os ($browser)";
    }
}
?>