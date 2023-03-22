<?php

namespace Cerbero\JsonParser\ValueObjects;

use function is_null;

/**
 * The parsing progress.
 *
 */
final class Progress
{
    /**
     * The current progress.
     *
     * @var int
     */
    private int $current = 0;

    /**
     * The total possible progress.
     *
     * @var int|null
     */
    private ?int $total = null;

    /**
     * Set the current progress
     *
     * @param int $current
     * @return self
     */
    public function setCurrent(int $current): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Retrieve the current progress
     *
     * @return int
     */
    public function current(): int
    {
        return $this->current;
    }

    /**
     * Set the total possible progress
     *
     * @param int|null $total
     * @return self
     */
    public function setTotal(?int $total): self
    {
        $this->total ??= $total;

        return $this;
    }

    /**
     * Retrieve the total possible progress
     *
     * @return int|null
     */
    public function total(): ?int
    {
        return $this->total;
    }

    /**
     * Retrieve the formatted percentage of the progress
     *
     * @return string|null
     */
    public function format(): ?string
    {
        return is_null($percentage = $this->percentage()) ? null : number_format($percentage, 1) . '%';
    }

    /**
     * Retrieve the percentage of the progress
     *
     * @return float|null
     */
    public function percentage(): ?float
    {
        return is_null($fraction = $this->fraction()) ? null : $fraction * 100;
    }

    /**
     * Retrieve the fraction of the progress
     *
     * @return float|null
     */
    public function fraction(): ?float
    {
        return $this->total ? $this->current / $this->total : null;
    }
}
