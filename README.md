# php_search_api_sample

## 環境

#### 言語
- PHP: 8.1.12

#### ライブラリ
- phpdotenv: 5.5

#### エディタ
- VsCode

## 環境構築

```
git clone https://github.com/kosimaru1997/php_search_api_sample.git
cd php_search_api_sample

touch .env // API_KEY、検索IDを設定する(.env.exampleを参考に設定してください)
php -S 0.0.0.0:8080
```

http://localhost:8080 にアクセスし画面が表示されれば成功です。

## 参考情報

Custom Search API 
- ドキュメント: https://developers.google.com/custom-search/v1/overview?hl=ja
- 接続情報: https://developers.google.com/custom-search/v1/reference/rest/v1/cse/list#request

