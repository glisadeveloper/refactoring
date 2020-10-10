<?php
/**
we use this class for task D)
*/
include 'class.books.php';
/**

A) AN ORDERING FUNCTION

1. The function gets two parameters, the list of books ($list) and the ordered list of
ids ($list_order).
2. The response is the ordered list of books.
3. If there’s an id in the $list_order that doesn’t exist in the $list of books, the
function should just ignore it.
4. If some books are in the $list but their id is not in the $list_order, this book
must be appended at the end of the list.
5. The function must be optimised in order to be fast enough even with huge
amounts of books (eg a million of records).

 */

function ordered_list​($list, $list_order) {
	$ordered = [];
	//In this way loop work faster, instead of being: for( $i=0; $i< count($list_order); $i++){ }
    $len = count($list_order);
    //Here we use iteration for ids
	for( $i=0; $i<$len; $i++){ //I use for loop instead of forech - reason to show that "for loop" does a good job
		$id = $list_order[$i];
		//search for id in passed $list array
		$id_exists = array_search($id, array_column($list, 'id'));
		// if id not exists on ordered we push new array from $list
		if(empty(array_search($id, array_column($ordered, 'id')))){
			array_push($ordered, $list[$id_exists]);
		}
	}
    // with array_merge we merge arrays $ordered with $list to new array ( requirement: 4)
    // with array_unique we eliminate duplication values from an array
	return array_unique(array_merge($ordered, $list), SORT_REGULAR);
}

//Single quotes is faster than using double quotes ( I am replaced )
$books = [
 [
 'id' => 23,
 'title' => 'The divine Comedy',
 'description' => 'Lorem ipsum dolor sit amet...'
 ],
 [
 'id' => 44,
 'title' => 'The woman from Zagreb',
 'description' => 'Lorem ipsum dolor sit amet, consectetur...'
 ],
 [
 'id' => 9,
 'title' => 'Blindness',
 'description' => 'Consectetur adipiscing elit...'
 ],
 [
 'id' => 1973,
 'title' => 'Faust',
 'description' => 'Tempor incididunt ut labore et dolore magna aliqua.'
 ]
];
$books_order = [44, 1973, 23, 9];
$ordered_books = ordered_list​($books, $books_order);
//print_r($ordered_books);

/**
B) A SEARCH FUNCTION

1. The function gets two parameters, the list of books ($list) and a list of search
terms ($search_terms).
2. The search must be case insensitive (“This” == “this”).
3. The response is a list of books that satisfies the search terms. A search term is
satisfied if it’s contained in title or in description. For example: the term “equa”
is satisfied for a title “Simple Mathematical Equations”.
4. The response should not contain duplicates so each book must appear once.
5. The function must be optimised in order to be fast enough even with big
amounts of books (eg a hundred thousand of records).

 */

function search_list($list, $search_terms) {
 	$results = [];
	 foreach ($search_terms as $search_term) {
	 	//strtolower we use for case sensitive ( we translate everything into lowercase to make the search successful )
	 	$title_or_description = strtolower($search_term);
		//search for title in passed $list array
		$title_exists = array_filter($list, function($item) use ($title_or_description) {
		 	return (strlen(str_replace($title_or_description, '', strtolower($item['title']))) <
			strlen(strtolower($item['title'])) ? true : false);
		 });

		// if title exists on passed $list array we push to $result
		if(!empty($title_exists)){
			array_push($results, $title_exists[0]);
		}

		//search for description in passed $list array
		$description_exists = array_filter($list, function($item) use ($title_or_description) {
		 	return (strlen(str_replace($title_or_description, '', strtolower($item['description']))) <
			strlen(strtolower($item['description'])) ? true : false);
		 });

		// if description exists on passed $list array we push to $result
		if(!empty($description_exists)){
			array_push($results, $description_exists[0]);
		}
	 }
	// with array_unique we eliminate duplication values from an array 
 	return array_unique($results, SORT_REGULAR);
}

//Single quotes is faster than using double quotes ( I am replaced )
$books = [
 [
 'id' => 44,
 'title' => 'The woman from Zagreb',
 'description' => 'Lorem ipsum dolor sit amet, consectetur...'
 ],
 [
 'id' => 1973,
 'title' => 'Tempor la dolore magna',
 'description' => 'Tempor incididunt ut labore et dolore magna aliqua.'
 ]
];
// I add uppercase to Woman to show the functions is working
$search_results = search_list($books, ['Woman', 'dolor sitt']); 
//print_r($search_results);


/**
C) A RANKING FUNCTION

1. The function (top_rank_list) gets two parameters: the list of books ($list) and
the rank list length ($rank_length).
2. We have three factors to consider while ranking: up_votes, down_votes,
publisher_stars.
3. The ranking depends 50% on publisher’s stars (publisher_stars) and 50%
on readers' votes (up_votes minus down_votes).
4. The response is a list of the top ranked books not longer than $rank_length.
5. The function must be optimised in order to be fast enough even with huge
amounts of books (eg a million of records).

*/

function top_rank_list($list, $rank_length) {
	// Add code to find top ranked items
    $top_ranked_items_list = [];
    //here we temporarily store rank ratings
    $top_ranked = [];
    //with foreach we count rank and push result on $top_ranked
	foreach ($list as $book) {
		$rank = 1 * $book['publisher_stars'];
		$rank += 2 * ($book['up_votes'] - $book['down_votes']);
		array_push($top_ranked, $rank);
	}	
    //we preventively check the requested books
	if($rank_length > count($top_ranked)){
		return 'Your list has '. count($top_ranked) . ' books';
	}
	//with rsort we get from the highest to the lowest rankings
	rsort($top_ranked);

    $len = count($top_ranked);
    //Here we use iteration for books
	for( $i=0; $i<$rank_length; $i++){ 
        foreach ($list as $book) {
        	$rank = 1 * $book['publisher_stars'];
		    $rank += 2 * ($book['up_votes'] - $book['down_votes']);
        	//I use === instead of ==, as the former strictly checks for a closed range which makes it faster
        	if($rank === $top_ranked[$i]){
        	   //if rank is a same as in temporary array we push to $top_ranked_items_list
        	   array_push($top_ranked_items_list, $book);
        	   break;
        	}
        }
	}
    return $top_ranked_items_list;
}

//Single quotes is faster than using double quotes ( I am replaced )
$books = [
 [
 'id' => 23,
 'title' => 'The divine Comedy',
 'description' => 'Lorem ipsum dolor sit amet...',
 'up_votes' => 5439,
 'down_votes' => 238,
 'publisher_stars' => 8.0
 ],
 [
 'id' => 9,
 'title' => 'Craziness',
 'description' => 'Consectetur adipiscing elit...',
 'up_votes' => 4109,
 'down_votes' => 98,
 'publisher_stars' => 6.5
 ],
 [
 'id' => 1973,
 'title' => 'Faust',
 'description' => 'The incididunt ut labore from et dolore magna aliqua.',
 'up_votes' => 3455,
 'down_votes' => 80,
 'publisher_stars' => 7.5
 ],
 [
 'id' => 1982,
 'title' => 'Ipsum',
 'description' => 'Vivamus pharetra bibendum turpis, non maximus eros luctus quis',
 'up_votes' => 1455,
 'down_votes' => 180,
 'publisher_stars' => 8.5
 ],
 [
 'id' => 92,
 'title' => 'Neque',
 'description' => 'Duis a lobortis lacus, ac laoreet magna. Duis facilisis neque eu augue sagittis porta',
 'up_votes' => 55,
 'down_votes' => 5180,
 'publisher_stars' => 1.5
 ]
 // more books...
];

$top5_books = top_rank_list($books, 5);
//print_r($top5_books);

/**
D) COMBINING IN A CLASS
	● reorder: uses the logic of the ordered_list function and changes the list on the current Class instance.
	● get: returns the list of books of the current Class instance.
	● search: returns the filtered list of books based on the search_list function logic.
	● top_rank: returns the top ranked books of the current Class instance. It’s
	  based on top_rank_list function and if we call it without parameters returns the top 10 books.

*/

//Get books - class
$books = [
 [
 'id' => 23,
 'title' => 'The divine Comedy',
 'description' => 'Lorem ipsum dolor sit amet...'
 ],
 [
 'id' => 44,
 'title' => 'The woman from Zagreb',
 'description' => 'Lorem ipsum dolor sit amet, consectetur...'
 ],
 [
 'id' => 9,
 'title' => 'Blindness',
 'description' => 'Consectetur adipiscing elit...'
 ],
 [
 'id' => 1973,
 'title' => 'Faust',
 'description' => 'Tempor incididunt ut labore et dolore magna aliqua.'
 ]
];
$books_order = [44, 1973, 23, 9];
$books = new books($books, $books_order);
$getBooks = $books->get();
//print_r($getBooks);

//Search books - class
$books = [
 [
 'id' => 44,
 'title' => 'The woman from Zagreb',
 'description' => 'Lorem ipsum dolor sit amet, consectetur...'
 ],
 [
 'id' => 1973,
 'title' => 'Tempor la dolore magna',
 'description' => 'Tempor incididunt ut labore et dolore magna aliqua.'
 ]
];

$books = new books($books, '');
$searchBoooks = $books->search(['woman', 'dolor sitt']);
//print_r($searchBoooks);

//top rank with 10 books
$books = [
 [
 'id' => 23,
 'title' => 'The divine Comedy',
 'description' => 'Lorem ipsum dolor sit amet...',
 'up_votes' => 5439,
 'down_votes' => 238,
 'publisher_stars' => 8.0
 ],
 [
 'id' => 9,
 'title' => 'Craziness',
 'description' => 'Consectetur adipiscing elit...',
 'up_votes' => 4109,
 'down_votes' => 98,
 'publisher_stars' => 6.5
 ],
 [
 'id' => 1973,
 'title' => 'Faust',
 'description' => 'The incididunt ut labore from et dolore magna aliqua.',
 'up_votes' => 3455,
 'down_votes' => 80,
 'publisher_stars' => 7.5
 ],
 [
 'id' => 1982,
 'title' => 'Ipsum',
 'description' => 'Vivamus pharetra bibendum turpis, non maximus eros luctus quis',
 'up_votes' => 1455,
 'down_votes' => 180,
 'publisher_stars' => 8.5
 ],
 [
 'id' => 92,
 'title' => 'Neque',
 'description' => 'Duis a lobortis lacus, ac laoreet magna. Duis facilisis neque eu augue sagittis porta',
 'up_votes' => 55,
 'down_votes' => 5180,
 'publisher_stars' => 1.5
 ],
  [
 'id' => 230,
 'title' => 'The divine Comedy 2',
 'description' => 'Lorem ipsum dolor sit amet...2',
 'up_votes' => 543,
 'down_votes' => 23,
 'publisher_stars' => 2.0
 ],
 [
 'id' => 19,
 'title' => 'Craziness 2',
 'description' => 'Consectetur adipiscing elit...2',
 'up_votes' => 410,
 'down_votes' => 8,
 'publisher_stars' => 2.5
 ],
 [
 'id' => 193,
 'title' => 'Faust 2',
 'description' => 'The incididunt ut labore from et dolore magna aliqua.2',
 'up_votes' => 355,
 'down_votes' => 280,
 'publisher_stars' => 3.5
 ],
 [
 'id' => 982,
 'title' => 'Ipsum 2',
 'description' => 'Vivamus pharetra bibendum turpis, non maximus eros luctus quis 2',
 'up_votes' => 155,
 'down_votes' => 18,
 'publisher_stars' => 4.5
 ],
 [
 'id' => 392,
 'title' => 'Neque 2',
 'description' => 'Duis a lobortis lacus, ac laoreet magna. Duis facilisis neque eu augue sagittis porta 2',
 'up_votes' => 155,
 'down_votes' => 518,
 'publisher_stars' => 5.5
 ]

 // more books...
];
$books = new books($books, '');
$top_rank = $books->top_rank(10);
//print_r($top_rank)

?>