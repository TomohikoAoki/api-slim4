<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\News;

use App\Domain\News\NewsRepository;
use App\Models\NewsContent as News;
use Exception;

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

    public function countAll()
    {
        return $this->news::count();
    }

    public function findAllWithPage(int $limit, int $page)
    {
        $offset = ($page - 1) * $limit;
        $data = $this->news::orderBy('created_at', 'desc')->offset($offset)->limit($limit)->select('id', 'title', 'created_at', 'thumb_filename')->get();

        return $data;
    }

    public function findCurrent()
    {
        return $this->news::orderBy('created_at', 'desc')->where('public', 1)->limit(3)->select('id', 'title', 'created_at', 'thumb_filename')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $data = $this->news::orderBy('created_at', 'desc')->select('id', 'title', 'created_at', 'thumb_filename')->get();

        return $data;
    }

    public function findId(int $id)
    {
        $data = $this->news::find($id);

        return $data;
    }

    public function findFromShopId(int $shopId)
    {
        $data = $this->news::find($shopId);

        return $data;
    }

    public function register(array $data)
    {
        try {
            $result = $this->news->create($data);
            return $result;
        } catch (Exception $e) {
            throw new Exception;
        }
    }

    /**
     * サムネ画像のURLを取得
     * @param array $data
     * @param int $id
     */
    public function update(array $data, int $id)
    {
        $result = $this->news->find($id)->update($data);

        return $result;
    }

    /**
     * 削除
     * @param int $id
     */
    public function delete(int $id)
    {
        $result = $this->news->destroy($id);

        return $result;
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
