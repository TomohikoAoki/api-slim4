<?php
declare(strict_types=1);

namespace App\Domain\News;

interface NewsRepository
{
    /**
     * @return News[]
     */
    public function findAll();

    public function countAll();

    public function findAllWithPage(int $limit, int $page);

    public function findCurrent();

    public function findId(int $id);

    public function findFromShopId(int $shopId);

    public function register(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function getThumbUrl(int $id);

}