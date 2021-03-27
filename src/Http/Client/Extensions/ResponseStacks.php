<?php

namespace KSuzuki2016\HttpClient\Http\Client\Extensions;

use Illuminate\Support\Arr;
use KSuzuki2016\HttpClient\Http\Client\HttpClientResponse;

/**
 * Trait ResponseStacks
 *
 * @mixin HttpClientResponse
 * @package KSuzuki2016\HttpClient\Http\Client\Extensions
 */
trait ResponseStacks
{
    /**
     * @var array
     */
    protected $stacks = [];

    /**
     * @param int $key
     * @return array|\ArrayAccess|mixed
     */
    public function stack(int $key = 0)
    {
        return Arr::get($this->stacks(), $key);
    }

    public function stacks(): array
    {
        if (!$this->stacks) {
            $this->stacks = json_decode('[' . $this->header('stacks') . ']', true);
        }
        return $this->stacks;
    }

    public function setStacks(array $stacks): self
    {
        $this->stacks = $stacks;
        return $this;
    }


}
