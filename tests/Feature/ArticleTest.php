<?php

namespace Tests\Feature;

use App\Article;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;
    //いいねされているかを判定するメソッドのテスト。
    // 1引数がnullの時に正しく動くか。falseがが返ってくるか
    // 2正しくいいねされているユーザーの数がカウントされているか。つまりtrueがが返ってくるか
    // 3いいねが一件もついていないケースにおいても正しい挙動をするか。falseが返ってくるか。
    //1
    public function testIsLikedByNull()
    {
        $article = factory(Article::class)->create();
        $result = $article->isLikedBy(null);

        //ここで$thisとなっているのはassertメソッドを持っているのがまだないため。
        //例えば$response = $this->get('hoge');とした場合には
        //$resonseの型がTestResponse型となっているのでそのまま返り値とassertメソッドを組み合わせられる。
        $this->assertFalse($result);
    }

    //2
    public function testIsLikedByTheUser()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create();

        //ここで実際にいいねをしているのと同じ挙動になる。
        //likesテーブルという多対多を結んでいる中間テーブルがある。
        //まず、$article->likes()とすることで、多対多のリレーション(BelongsToManyクラスのインスタンス)が返ります。
        //この多対多のリレーションでは、attachメソッドが使用できます。
        //これによって作成したarticleidの記事に対してuser_idがここで紐づく(いいねした)ことになる
        //つまり逆もおそらくできる。仕様としては変だがコード的には。
        $article->likes()->attach($user);

        //そしてリレーションが必要な動的プロパティを呼び出している。
        $result = $article->isLikedBy($user);

        $this->assertTrue($result);
    }

    //3
    public function testIsLikedByAnother()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create();
        $another = factory(User::class)->create();
        $article->likes()->attach($another);

        $result = $article->isLikedBy($user);

        $this->assertFalse($result);
    }
}
