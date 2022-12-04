<html>
<head>
<link rel="stylesheet" type="text/css" href="index.css">
  <title>PHP search api</title>
</head>

<body>

<?php
  // 環境変数の読み込み
  require __DIR__ . '/vendor/autoload.php';
  Dotenv\Dotenv::createImmutable(__DIR__)->load();
  $URL = $_ENV['SEARCH_URL'];
  $API_KEY = $_ENV['SEARCH_API_KEY'];
  $CX = $_ENV['SEARCH_CX'];

  // formの受け取り(クエリパラメータの取得)
  $input_word = (isset($_GET['word'])) ?$_GET['word'] : null;
  $start = (isset($_GET['start'])) ? $_GET['start'] : 0;

  $items = null;

  if ($input_word != null) {
    // 半角スペース、全角スペースの置換(URL設定のため)
    $word = str_replace(' ', '+', $input_word);
    $word = str_replace('　', '+', $word);

    $request_url = "{$URL}?key={$API_KEY}&cx={$CX}&q={$word}&start={$start}";
    $result_json = file_get_contents($request_url, true);
    $result = json_decode($result_json, true);
    $items = $result['items'];
    $next = $result['queries']['nextPage'];
  }
?>

  <div class="search">
    <form method="GET" action="index.php">
      <input class="search-form" name="word" type="text" value="<?php echo $input_word; ?>" placeholder="looking for">
      <input class="search-btn" type="submit" value="Search">
    </form>
  </div>

  <div class="contents">

  <?php if ($items == null) : ?>
    <p>検索ワードを入力してください</p>
  <?php else : ?>
    <?php foreach ($items as $key => $item) : ?>
    <blockquote class="wp-block-quote">
      <a href="<?php echo $item['link']; ?>">
        <?php echo $item['title']; ?>
      </a>
      <p>
        <?php echo $item['snippet']; ?>
      </p>
    </blockquote>
    <?php endforeach; ?>
  <?php endif; ?>

  </div>

</body>
</html>
