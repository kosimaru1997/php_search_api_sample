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
  // 入力内容が、「半角スペース」、「全角スペース」、「+」だけの場合$wordをnullをする
  $input_word =
  (isset($_GET['word']) && !preg_match('/^[\s|\+]+$/u' ,$_GET['word']))
    ? $_GET['word']
    : null;

  $start = (isset($_GET['start'])) ? $_GET['start'] : 1;

  $items = null;

  if ($input_word != null) {
    // 半角スペース、全角スペースの置換(URL設定のため)
    $word = str_replace(' ', '+', $input_word);
    $word = str_replace('　', '+', $word);

    $request_url = "{$URL}?key={$API_KEY}&cx={$CX}&q={$word}&start={$start}";

    try {
      // エラー時の処理を登録
      set_error_handler(function(){
        throw new Exception();
      });

      $result_json = file_get_contents($request_url, true);
      $result = json_decode($result_json, true);
      $items = $result['items'];
      // 前ページのインデックスを取得
      $previousPage =
        array_key_exists('previousPage', $result['queries'])
          ? array_shift($result['queries']['previousPage'])
          : null;
      $previousIndex =
        $previousPage != null && array_key_exists('startIndex', $previousPage)
          ? $previousPage['startIndex']
          : null;
      // 次ページのインデックスを取得
      $nextPage =
        array_key_exists('nextPage', $result['queries'])
          ? array_shift($result['queries']['nextPage'])
          : null;
      $nextIndex =
        $nextPage != null && array_key_exists('startIndex', $nextPage)
        ? $nextPage['startIndex']
        : null;
    } catch(Exception $e) {
      echo "カスタム検索APIの呼び出し時に予期せぬエラーが発生しました";
    } finally {
      restore_error_handler();
    };
  }
?>

  <div class="search">
    <form method="GET" action="index.php">
      <input class="search-form" name="word" type="text" value="<?php echo $input_word; ?>" placeholder="looking for">
      <input class="search-btn" type="submit" value="Search">
    </form>
  </div>

  <?php if ($items == null) : ?>
    <p>検索ワードを入力してください</p>
  <?php else : ?>
  <div class="contents">
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
  </div>

  <div class="page-nate">
    <ul class="page-nate-list">
      <?php if ($previousIndex != null) : ?>
        <a href=<?php echo "/?word={$word}&start={$previousIndex}" ?>><li>前へ</li></a>
      <?php endif; ?>
      <?php if ($nextIndex != null) : ?>
      <a href=<?php echo "/?word={$word}&start={$nextIndex}" ?>><li>次へ</li></a>
      <?php endif; ?>
    </ul>
  </div>
  <?php endif; ?>

</body>
</html>
