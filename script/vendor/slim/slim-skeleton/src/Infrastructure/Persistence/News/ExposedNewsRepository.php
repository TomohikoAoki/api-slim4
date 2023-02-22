<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\News;

use App\Domain\News\NewsRepository;
use App\Models\NewsContent as News;
use Illuminate\Database\Eloquent\Collection;
use App\Domain\News\NewsNotFoundException;
use App\Domain\News\NewsDomainException;

class ExposedNewsRepository implements NewsRepository
{
    /**
     * @var News
     */
    private $news;

    public function __construct()
    {
        $this->news = new News();
    }

    /**
     * ニュース全件数
     * 
     * @return int
     */
    public function countAll(): int
    {
        return $this->news::count();
    }

    /**
     * ニュース記事一覧件数　店舗ごと
     * 
     * @param int $shopId
     * 
     * @return int
     */
    public function countByShop(int $shopId): int
    {
        return $this->news::query()
            ->whereRaw("JSON_CONTAINS(shop_ids," . "'{$shopId}'," . "'$')")
            ->count();
    }

    /**
     * ニュース一覧　ページ分けで
     * 
     * @param int $limit 表示数
     * @param int $page ページ
     * 
     * @return Collection
     * 
     * @throws NewsNotFoundException
     * 
     */
    public function findAllWithPage(int $limit, int $page = 1): Collection
    {
        $offset = ($page - 1) * $limit;
        $data = $this->news::query()
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->select('id', 'title', 'created_at', 'thumb_filename')
            ->get();

        if (!isset($data) || $data->isEmpty()) {
            throw new NewsNotFoundException();
        }

        return $data;
    }

    /**
     * ニュース一覧　店舗ごと　ページネーション付き
     * 
     * @param int $shopId ショップID
     * @param int $limit 表示数
     * @param int $page 現在のページ
     * 
     * @return Collection
     * 
     * @throws NewsNotFoundException
     * 
     */
    public function findByShopId(int $shopId, int $limit, int $page = 1): Collection
    {
        $offset = ($page - 1) * $limit;
        $data = $this->news::query()
            ->whereRaw("JSON_CONTAINS(shop_ids," . "'{$shopId}'," . "'$')")
            ->orderBy('created_at', 'desc')
            ->offset($offset)->limit($limit)
            ->select('id', 'title', 'created_at', 'thumb_filename')
            ->get();

        if (!isset($data) || $data->isEmpty()) {
            throw new NewsNotFoundException();
        }

        return $data;
    }

    /**
     * パブリック設定の最新３件
     * 
     *  @return Collection
     * 
     * @throws NewsNotFoundException
     * 
     */
    public function findCurrent(): Collection
    {
        $data = $this->news::query()
            ->orderBy('created_at', 'desc')
            ->where('public', 1)
            ->limit(3)
            ->select('id', 'title', 'created_at', 'thumb_filename')
            ->get();

        if (!isset($data) || $data->isEmpty()) {
            throw new NewsNotFoundException();
        }

        return $data;
    }

    /**
     * パブリック設定の最新３件 店舗ごと
     * 
     * @param int $shopId
     * 
     * @return Collection
     * 
     * @throws NewsNotFoundException
     * 
     */
    public function findCurrentByShop(int $shopId): Collection
    {
        $data = $this->news::query()
            ->whereRaw("JSON_CONTAINS(shop_ids," . "'{$shopId}'," . "'$')")
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->select('id', 'title', 'created_at', 'thumb_filename')
            ->get();

        if (!isset($data) || $data->isEmpty()) {
            throw new NewsNotFoundException();
        }

        return $data;
    }

    /**
     * ニュース記事　１件
     * @param int $id
     * 
     * @throws NewsNotFoundException
     * 
     * @return News
     */
    public function findId(int $id): News
    {
        $data = $this->news::find($id);

        if (!isset($data)) {
            throw new NewsNotFoundException();
        }

        return $data;
    }

    /**
     * ニュース記事　新規作成
     * @param array $data
     * @return News
     * @throws NewsDomainException
     */
    public function register(array $data): News
    {
        try {
            $result = $this->news->create($data);
            return $result;
        } catch (Exception $e) {
            throw new NewsDomainException($e->getMessage());
        }
    }

    /**
     * ニュース記事　更新
     * @param array $data
     * @param int $id
     * @return bool
     * @throws NewsDomainException
     */
    public function update(array $data, int $id): bool
    {
        try {
            $result = $this->news->find($id)->update($data);
            return $result;
        } catch (Exception $e) {
            throw new NewsDomainException($e->getMessage());
        }
    }

    /**
     * 削除
     * @param int $id
     * @return int
     * @throws NewsDomainException
     */
    public function delete(int $id): int
    {
        try {
            $result = $this->news->destroy($id);
            return $result;
        } catch (Exception $e) {
            throw new NewsDomainException($e->getMessage());
        }
    }

    /**
     * サムネ画像のURLを取得
     * @param int $id
     */
    public function getThumbUrl(int $id)
    {
        $filename = $this->news->where('id', $id)->value('thumb_filename');

        if ($filename) {
            return PUBLIC_URI . UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename;
        }
        return null;
    }
}
