<?php
$allImages = [];

if (isset($_GET['search'])) {

    $query = urlencode($_GET['search']);
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $filter = $_GET['filter'] ?? "";

    $access_key_unsplash = "CVIUtNuMDIbvCHnYTobrqSAcsEq2muf9-LvNpFH9wjE";
    $url_unsplash = "https://api.unsplash.com/search/photos?query=$query&client_id=$access_key_unsplash&page=$page&per_page=20";

    $response = file_get_contents($url_unsplash);
    $data = json_decode($response, true);

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
    $access_key_pixabay = "53563226-9f711b527fabe9b621944ae54";
    $url_pixabay = "https://pixabay.com/api/?key=$access_key_pixabay&q=$query&image_type=photo&page=$page&per_page=20";

    $response2 = file_get_contents($url_pixabay);
    $data2 = json_decode($response2, true);

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
    if ($filter === "az") {
        usort($allImages, fn($a, $b) => strcmp($a["title"], $b["title"]));
    } elseif ($filter === "za") {
        usort($allImages, fn($a, $b) => strcmp($b["title"], $a["title"]));
    } elseif ($filter === "likes_high") {
        usort($allImages, fn($a, $b) => $b["likes"] - $a["likes"]);
    } elseif ($filter === "likes_low") {
        usort($allImages, fn($a, $b) => $a["likes"] - $b["likes"]);
    } elseif ($filter === "width_high") {
        usort($allImages, fn($a, $b) => $b["width"] - $a["width"]);
    } elseif ($filter === "width_low") {
        usort($allImages, fn($a, $b) => $a["width"] - $b["width"]);
    }

    foreach ($allImages as $img) {

        $small = $img["small"];
        $full  = $img["full"];
        $rawTitle  = $img["title"] ?? "";
        $titleText = htmlspecialchars(
            ucwords(trim($rawTitle ?: "Untitled Image"))
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

}
