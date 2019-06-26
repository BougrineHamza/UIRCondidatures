//Pour créer des résa


$router->get('/payload', function () use ($router) {
    $formations = \App\UirFormation::with('concourdate')->get();

    $times = ['14:30',
                  '14:50',
                  '15:10',
                '15:30',
                '15:50',
                '16:10',
                '16:30',
                '16:50',
                '17:10',
                '17:30'];

                $i = 1;
        foreach($formations as  $formation){
        
                foreach($formation->concourdate as $date){

                        foreach($times as $time){
                        
                        $concourtime = new \App\ConcourTime;
                        
                        $concourtime->concour_date_id = $date->id;
                        $concourtime->jury_id = $i;
                        $concourtime->uir_formation_id = $formation->id;
                        $concourtime->time = $time;
                        $concourtime->save();

                        }
        }       
		$i = $i + 1;
		}
});