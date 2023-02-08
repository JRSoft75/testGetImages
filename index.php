<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Результат</title>
</head>
<body>

<?php
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    $urlParsed = parse_url($url);
    $html = file_get_contents($url);
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $images = $doc->getElementsByTagName('img');
    $imageCount = $images->length;

    $totalSize = 0;
    foreach ($images as $image) {
        $src = $image->getAttribute('src');
        $size = getRemoteImageSize($src);
        $totalSize += $size;
    }

    echo '<table>';
    $i = 0;
    foreach ($images as $image) {
        if ($i % 4 === 0) {
            echo '<tr>';
        }
        echo '<td><img width="200" src="' . $image->getAttribute('src') . '" /></td>';
        if ($i % 4 === 3) {
            echo '</tr>';
        }
        $i++;
    }
    echo '</table>';

    echo '<p>На странице обнаружено ' . $imageCount . ' изображений на ' . round(
            $totalSize / 1024 / 1024,
            2
        ) . 'Мб.</p>';
} else {
    ?>
    <form method="post">
        <input width="300" name="url" id="url" value="Enter URL">
        <button type="submit">Го</button>
    </form>
    <?php
}


function getRemoteImageSize($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    curl_close($ch);
    return $size;
}

?>

</body>
</html>
