<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        //ここで$thisとはTestCaseを継承したArticleControllertestのクラスを指す。
        //TestCaseクラスを継承したクラスでは、getメソッドが使える。
        $response = $this->get(route('articles.index'));

        //$responseの型はIlluminate\Foundation\Testing\TestResponse
        //TestResponseクラスではassertHogeHogeメソッドが使える。
        $response->assertStatus(200)->assertViewIs('articles.index');
        //assertStatus()は渡したステータスコードとreponseのstatusCodeが同じかassert。ちなみにassertOK()も同様に200チェックする
        //assertStatus()はTestResponseクラスのインスタンスを返すためそのままメソッドチェーンが可能。
        //assertViewIs()は見ているファイルが、引数で渡したファイルであるかの検証。

    }

    //未ログイン状態(ゲスト)で投稿しようとした場合 
    public function testGuestCreate()
    {
        //ログインが必要なページに未ログインでアクセス
        $response = $this->get(route('articles.create'));
        //変数$responseには未ログイン状態で記事投稿画面にアクセスした時のレスポンスが代入されます。
        $response->assertRedirect(route('login'));
    }

    //ログイン状態で投稿しようとした場合 
    public function testAuthCreate()
    {
        //ファクトリをを利用してUserモデルをデータベース(sqlite。phpunit.xmlに環境変数としてデフォでセットしてある)
        //前提としてそのモデルのファクトリが存在する必要がある。
        $user = factory(User::class)->create();

        //ログインした状態でgetリクエストをを投げる
        $response = $this->actingAs($user)->get(route('articles.create'));
        //仮にリダイレクトされていたな場合のステータスコードは302
        $response->assertStatus(200)->assertViewIs('articles.create');
    }
}
