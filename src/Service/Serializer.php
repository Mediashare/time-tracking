<?php
namespace Mediashare\Service;

Class Serializer {
    /**
     * Convert json report file to Object 
     *
     * @param string $file
     * @return void
     */
    public function read(string $file) {
        if (file_exists($file)):
            $file = file_get_contents($file);
            if ($file):
                $tracking_array = json_decode($file, true);
                if ($tracking_array):
                    $tracking = $this->arrayToObject($tracking_array, 'Tracking');
                    // $tracking->report = $this->arrayToObject($tracking->report, 'Report');
                    foreach ($tracking->commits ?? [] as $index => $commit):
                        $tracking->commits[$index] = $this->arrayToObject($commit, 'Commit');
                        if (!empty($tracking->commits[$index]->step)): // Old version compability
                            $tracking->commits[$index]->steps[] = $this->arrayToObject($tracking->commits[$index]->step, 'Step');
                        else:
                            foreach ($tracking->commits[$index]->steps ?? [] as $step_index => $step):
                                $tracking->commits[$index]->steps[$step_index] = $this->arrayToObject($step, 'Step');
                            endforeach;
                        endif;
                    endforeach;
                    foreach ($tracking->steps ?? [] as $index => $step):
                        $tracking->steps[$index] = $this->arrayToObject($step, 'Step');
                    endforeach;
                    // Return Tracking
                    if (!empty($tracking)):return $tracking;endif;
                endif;
            endif;
        endif;

        return false;
    }

    public function arrayToObject(array $array, string $class_name) {
        $serialized = unserialize(sprintf('O:%d:"%s"%s', strlen('Mediashare\Entity\\' . $class_name), 'Mediashare\Entity\\' . $class_name, strstr(serialize($array), ':')));
        return $serialized;
    }
}
