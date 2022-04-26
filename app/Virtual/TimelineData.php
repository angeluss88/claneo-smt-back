<?php

namespace App\Virtual;

/**
 * @OA\Schema(
 *     type="object",
 *     title="Timeline Data",
 *     description="Content Strategy Data per Day",
 * )
 */
class TimelineData
{
    /**
     * @OA\Property(
     *     title="date",
     *     description="date",
     *     example="metricValue",
     * )
     *
     * @var string
     */
    public $date;
 }
