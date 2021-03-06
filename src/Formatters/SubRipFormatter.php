<?php

namespace SubtitleToolbox\Formatters;

use SubtitleToolbox\StringHelpers;
use SubtitleToolbox\Subtitle;
use SubtitleToolbox\SubtitleCue;

class SubRipFormatter extends SubtitleFormatter
{
    function format(Subtitle $subtitle, array $options = []): string
    {
        $output = "";
        foreach ($subtitle->getCues() as $cueIndex => $cue) {
            if ($cueIndex > 0) {
                $output .= StringHelpers::UNIX_LINE_ENDING;
            }
            $output .= $cueIndex + 1 . StringHelpers::UNIX_LINE_ENDING;
            $output .= $this->formatCue($cue, $options);
            $output .= StringHelpers::UNIX_LINE_ENDING;
        }

        return StringHelpers::addUtf8Bom($output);
    }


    private function formatCue(SubtitleCue $cue, array $options)
    {
        $startHour   = str_pad(floor($cue->getStart() / 3600), 2, "0", STR_PAD_LEFT);
        $startMinute = str_pad(floor($cue->getStart() / 60) % 60, 2, "0", STR_PAD_LEFT);
        $startSecond = str_pad(floor($cue->getStart()) % 60, 2, "0", STR_PAD_LEFT);
        $startMillis = str_pad(round(($cue->getStart() - floor($cue->getStart())) * 1000), 3, "0", STR_PAD_LEFT);

        $endHour   = str_pad(floor($cue->getEnd() / 3600), 2, "0", STR_PAD_LEFT);
        $endMinute = str_pad(floor($cue->getEnd() / 60) % 60, 2, "0", STR_PAD_LEFT);
        $endSecond = str_pad(floor($cue->getEnd()) % 60, 2, "0", STR_PAD_LEFT);
        $endMillis = str_pad(round(($cue->getEnd() - floor($cue->getEnd())) * 1000), 3, "0", STR_PAD_LEFT);

        $time  = "$startHour:$startMinute:$startSecond,$startMillis --> $endHour:$endMinute:$endSecond,$endMillis";
        $lines = implode(StringHelpers::UNIX_LINE_ENDING, $cue->getLines());

        // strip xml tags depending on option settings
        $lines = in_array(parent::OPTION_STRIP_ALL_XML_TAGS, $options)
            ? strip_tags($lines)
            : strip_tags($lines, "<b><u><i><font>");


        return $time . StringHelpers::UNIX_LINE_ENDING . $lines;
    }
}
