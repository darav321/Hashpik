<?php
$allImages = [];

if (isset($_GET['search'])) {

    $query  = urlencode($_GET['search']);
    $page   = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $filter = $_GET['filter'] ?? "";

    /* ================= CONFIG ================= */
    $PER_PAGE      = 20;   // images per page (final)
    $MAX_API_PAGES = 5;    // fetch 5 API pages (adjust carefully)
    /* ========================================== */

    $access_key_unsplash = "CVIUtNuMDIbvCHnYTobrqSAcsEq2muf9-LvNpFH9wjE";
    $access_key_pixabay  = "53563226-9f711b527fabe9b621944ae54";

    /* ========== FETCH MULTIPLE API PAGES ========== */
    for ($p = 1; $p <= $MAX_API_PAGES; $p++) {

        // UNSPLASH
        $url_unsplash = "https://api.unsplash.com/search/photos?query=$query&client_id=$access_key_unsplash&page=$p&per_page=20";
        $data = json_decode(@file_get_contents($url_unsplash), true);

        if (!empty($data['results'])) {
            foreach ($data['results'] as $img) {
                $allImages[] = [
                    "title"  => $img["alt_description"] ?: "",
                    "small"  => $img["urls"]["small"],
                    "full"   => $img["urls"]["full"],
                    "likes"  => $img["likes"],
                    "width"  => $img["width"],
                    "height" => $img["height"],
                    "source" => "unsplash"
                ];
            }
        }

        // PIXABAY
        $url_pixabay = "https://pixabay.com/api/?key=$access_key_pixabay&q=$query&image_type=photo&page=$p&per_page=20";
        $data2 = json_decode(@file_get_contents($url_pixabay), true);

        if (!empty($data2['hits'])) {
            foreach ($data2['hits'] as $img) {
                $allImages[] = [
                    "title"  => $img["tags"],
                    "small"  => $img["previewURL"],
                    "full"   => $img["largeImageURL"],
                    "likes"  => $img["likes"],
                    "width"  => $img["imageWidth"],
                    "height" => $img["imageHeight"],
                    "source" => "pixabay"
                ];
            }
        }
    }

    /* ========== GLOBAL SORTING ========== */
    switch ($filter) {
        case "az":
            usort($allImages, fn($a, $b) => strcmp($a["title"], $b["title"]));
            break;
        case "za":
            usort($allImages, fn($a, $b) => strcmp($b["title"], $a["title"]));
            break;
        case "likes_high":
            usort($allImages, fn($a, $b) => $b["likes"] - $a["likes"]);
            break;
        case "likes_low":
            usort($allImages, fn($a, $b) => $a["likes"] - $b["likes"]);
            break;
        case "width_high":
            usort($allImages, fn($a, $b) => $b["width"] - $a["width"]);
            break;
        case "width_low":
            usort($allImages, fn($a, $b) => $a["width"] - $b["width"]);
            break;
    }

    /* ========== MANUAL PAGINATION ========== */
    $totalImages = count($allImages);
    $totalPages  = max(1, ceil($totalImages / $PER_PAGE));

    $page   = min($page, $totalPages);
    $offset = ($page - 1) * $PER_PAGE;

    $pageImages = array_slice($allImages, $offset, $PER_PAGE);

    /* ========== IMAGE GRID ========== */
    foreach ($pageImages as $img) {

        $titleText = htmlspecialchars(
            ucwords(trim($img["title"] ?: "Untitled Image"))
        );

        echo "
        <div class='w-full rounded-xl shadow-lg bg-white overflow-hidden hover:shadow-xl transition'>
            <img src='{$img['small']}'
                 data-full='{$img['full']}'
                 class='w-full h-64 object-cover previewImg cursor-pointer'>
            <div class='p-3 bg-gray-100'>
                <p class='text-sm font-medium text-gray-700 truncate'>
                    $titleText
                </p>
            </div>
        </div>";
    }

    /* ========== PAGINATION BUTTONS (5–7) ========== */
    echo "<div class='col-span-full flex justify-center items-center gap-2 mt-8'>";

    if ($page > 1) {
        echo "<button onclick='loadImages(" . ($page - 1) . ")' class='px-3 py-1 border rounded'>Prev</button>";
    }

    $range = 3;
    $start = max(1, $page - $range);
    $end   = min($totalPages, $page + $range);

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            echo "<span class='px-3 py-1 bg-[#CC774A] text-white rounded'>$i</span>";
        } else {
            echo "<button onclick='loadImages($i)' class='px-3 py-1 border rounded'>$i</button>";
        }
    }

    if ($page < $totalPages) {
        echo "<button onclick='loadImages(" . ($page + 1) . ")' class='px-3 py-1 border rounded'>Next</button>";
    }

    echo "</div>";
}
?>
