
<?php
    header("Content-type: application/json; charset=utf-8");
    header('Access-Control-Allow-Origin: *');

	
//クライアントから
//ユーザーIDを取得
      //$user_id = $_POST["user_id"];
//エネミーIDを取得
      //$enemy_id = $_POST["enemy_id"];
    $user_id = 1;
    $enemy_id = 2;
//ユーザーIDを使ってMySQLのユーザーテーブルからデータを取得する。
    $dsn = 'mysql:dbname=lesson;host=localhost:3306';//dbname=データベースの名前 host = サーバのアドレスorホスト名
    $user = 'root';//接続するアカウント
    $password = '';//今回は未設定、自分でMySQLにパスワードをかけてる人はここに記述してください。インストールして何も設定を変えて無ければ無視で大丈夫
    try {
        //MySQLとの接続準備を開始
        $dbh = new PDO($dsn, $user, $password);
        $dbh->beginTransaction();
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->query('SET NAMES sjis');
        //準備ここまで
        //選択処理開始
        //MySQLからデータを取得するためのSQLを準備
        //使用するSQL   'SELECT <取得するカラム> FROM <テーブル名> WHERE <検索する条件>
        //今回はユーザーと敵のデータを取得するので
        //取得するカラム　=> *     *はテーブルが持つカラムの全てを宣言しているのと同じ。
        //テーブル名　    => player
        //検索する条件　  => user_id = ?            ?は後で宣言するための部分。ここで1とか決まった値を入れると使いまわせなくなります。
        $sql = '';
        $stmt = $dbh->prepare($sql);
        //プレイヤーデータをDBからSELECT
        $stmt->execute(array($user_id));//ここでSQLの?部分への数値入力を行っている。
        //プレイヤーデータを取得
        foreach ($stmt as $row) {
            //foreachとは、foreach(配列 as 適当な変数名)と宣言したとき配列の要素を一つだけ取り出して適当な
            //変数名に入れる。{}の中に書かれた処理が終わると取り出した配列の次の要素を抜き出して適当な変数名に入れる
            //という処理を繰り返す。配列の次の要素が無くなった時点でループを抜ける。

            //SELECTしてきたデータを全て展開している。どんな値が入ってるかを確認するにはvar_dumpという関数が便利です。
            //var_dump($userData);
            $userData = $row;
        }
        //エネミーのデータを取得するSQLを作成
        //戦闘計算ここから//////
        //自分と敵がそれぞれ攻撃を行う。
        //戦闘の計算は　防御側の体力　- (攻撃側の攻撃力　- 防御側の防御力)  = 防御側の新しい体力
        //戦闘計算ここまで//////

        //更新処理開始
        //データを更新するときのSQLはUPDATEを使用する
        //使用するSQL　UPDATE <テーブル名> SET <更新するカラム>  WHERE <検索する条件>
        //今回は
        //テーブル名　=> player
        //カラム名　  => hp = ?
        //検索条件　  => user_id = ?
        $sql = '';
        $stmt = $dbh->prepare($sql);
        //プレイヤーのデータをテーブルへ更新
        if(!$flag){
            throw new Exception("プレイヤーのupdateに失敗しました。");
        }
        //エネミーのデータをテーブルへ更新
        if(!$flag){
            throw new Exception("エネミーのupdateに失敗しました。");
        }
        $result = array();
        $result['player_hp'] = 1;
        $result['enemy_hp'] = 2;
        $dbh->commit();//全てが正常に処理を行えたらデータをコミット（DBへの更新の確定）
        //更新処理終了
    } catch (PDOException $e) {
        echo 'Error:' . $e->getMessage();
        $dbh->rollBack();//エラーを吐いた場合はここまでに行ったDBへの更新を全て無かったことにする。
        exit();
    }

    //クライアント（スマホとかPCとかユーザーの持つ端末）へのデータ送信
    echo json_encode($result);

?>