<?php


namespace App\Admin\Metrics;


use App\Models\CheckRecord;
use App\Models\CheckTrack;
use App\Models\HardwareRecord;
use Closure;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Card;
use Illuminate\Contracts\Support\Renderable;

class CheckHardwarePercentage extends Card
{
    /**
     * @param string|Closure|Renderable|LazyWidget $content
     *
     * @return $this
     */
    public function content($content)
    {
        if ($content instanceof LazyGrid) {
            $content->simple();
        }

        $hardware_records_all = HardwareRecord::all()->count();
        $check_record = CheckRecord::where('check_item', 'software')->where('status', 1)->first();
        if (!empty($check_record)) {
            $check_tracks_counts = CheckTrack::where('check_id', $check_record->id)
                ->where('status', '!=', 0)
                ->get()
                ->count();
            $done_counts = ($hardware_records_all - $check_tracks_counts) . ' / ' . $hardware_records_all;
            $percentage = ($hardware_records_all - $check_tracks_counts) / $hardware_records_all;
        } else {
            $done_counts = '未找到在列的盘点任务';
            $percentage = 0;
        }

        $html = <<<HTML
<div class="info-box" style="margin-bottom: 0;">
  <span class="info-box-icon bg-info"><i class="far fa-bookmark"></i></span>
  <div class="info-box-content">
    <span class="info-box-text">硬件盘点进度</span>
    <span class="info-box-number">{$done_counts}</span>
    <div class="progress">
      <div class="progress-bar bg-info" style="width: {$percentage}%"></div>
    </div>
    <span class="progress-description">
      {$percentage}%
    </span>
  </div>
</div>
HTML;

        $this->content = $this->lazyRenderable($html);
        $this->noPadding();

        return $this;
    }
}
