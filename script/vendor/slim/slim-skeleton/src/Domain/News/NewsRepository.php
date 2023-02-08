<?php
declare(strict_types=1);

namespace App\Domain\News;

interface NewsRepository
{

    /**
     * ニュース全件数
     */
    public function countAll();

    /**
     * ニュース一覧　ページ分けで
     */
    public function findAllWithPage(int $limit, int $page);

    /**
     * パブリック設定の最新３件
     * 
     */
    public function findCurrent();

    //ニュース記事　１ページ
    public function findId(int $id);

    public function findByShopId(int $shopId, int $limit, int $page);

    //ニュース登録
    public function register(array $data);

    //ニュース更新
    public function update(array $data, int $id);

    //ニュース削除
    public function delete(int $id);

    //サムネ画像url
    public function getThumbUrl(int $id);

}