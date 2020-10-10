<?php
class books{
	protected $ordered = [];
	protected $list;
	protected $list_order;
	protected $results = [];
    protected $top_ranked_items_list = [];
	protected $top_ranked = [];
    public function __construct($list, $list_order){
       $this->list = $list;
       $this->list_order = $list_order;
    }

    private function reorder(){
        $len = count($this->list_order);
	   	for( $i=0; $i<$len; $i++){
			$id = $this->list_order[$i];
			$id_exists = array_search($id, array_column($this->list, 'id'));
			if(empty(array_search($id, array_column($this->ordered, 'id')))){
				array_push($this->ordered, $this->list[$id_exists]);
			}
		}
	return array_unique(array_merge($this->ordered, $this->list), SORT_REGULAR);
    }

    public function get() {
        return $this->reorder();
	}

	public function search($search_terms) {
		 foreach ($search_terms as $search_term) {
		 	$title_or_description = strtolower($search_term);
			$title_exists = array_filter($this->list, function($item) use ($title_or_description) {
			 	return (strlen(str_replace($title_or_description, '', strtolower($item['title']))) <
				strlen(strtolower($item['title'])) ? true : false);
			 });

			if(!empty($title_exists)){
				array_push($this->results, $title_exists[0]);
			}

			$description_exists = array_filter($this->list, function($item) use ($title_or_description) {
			 	return (strlen(str_replace($title_or_description, '', strtolower($item['description']))) <
				strlen(strtolower($item['description'])) ? true : false);
			 });

			if(!empty($description_exists)){
				array_push($this->results, $description_exists[0]);
			}
		 }
	 	return array_unique($this->results, SORT_REGULAR);
    }

    public function top_rank($rank_length){
		foreach ($this->list as $book) {
			$rank = 1 * $book['publisher_stars'];
			$rank += 2 * ($book['up_votes'] - $book['down_votes']);
			array_push($this->top_ranked, $rank);
		}	
		if($rank_length > count($this->top_ranked)){
			return 'Your list has '. count($this->top_ranked) . ' books';
		}
		rsort($this->top_ranked);
	    $len = count($this->top_ranked);

		for( $i=0; $i<$rank_length; $i++){ 
	        foreach ($this->list as $book) {
	        	$rank = 1 * $book['publisher_stars'];
			    $rank += 2 * ($book['up_votes'] - $book['down_votes']);
	        	if($rank === $this->top_ranked[$i]){
	        	    array_push($this->top_ranked_items_list, $book);
	        	   break;
	        	}
	        }
		}
	    return $this->top_ranked_items_list;
    }

}
?>