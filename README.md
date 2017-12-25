# Tree builder

The class for building tree array structure

## Installation
Run:
```bash
	composer require fomvasss/tree-builder
```

---
## Usage
Create your own class for your item that inherits `Fomvasss\TreeBuilder\TreeBuilder` class.

For example, we create `CommentTreeItem` and `UserTreeItem`:

```php
<?php

namespace App\Managers\TreeBuilder;

use Fomvasss\TreeBuilder\TreeBuilder;

class CommentTreeItem extends TreeBuilder
{
    protected $userTreeItem;

    public function __construct(UserTreeItem $userTreeItem)
    {
        $this->userTreeItem = $userTreeItem;
    }

    protected function transform(array $item)
    {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'created_at' => optional(\Carbon\Carbon::parse($item['created_at']))->toIso8601String(),
            'user' => $this->userTreeItem->getItem($item['user'])
        ];
    }
}
```

In controller we can use next methods:

- `getTree(array $items)` - get tree structure
- `getItem(array $item)` - get one item
- `getItems(array $items)` - get array items
- `hideDepth()` - do not show field `depth` in result array
- `setExceptKeys(array $keys)` - set except keys for result array

__! Your array must have `id` & `parent_id` keys for building tree__

```php
<?php
class CommentController
{
    
    public function index(Request $request)
    {
        $comments = \App\Models\Comment::where('article_id', $request->article_id)->get();
		$commentTreeBuilder = new \App\Managers\TreeBuilder\CommentTreeItem();
		
        return \response()->json(['data' => $commentTreeBuilder->getTree($comments->toArray())]);
    }
    
	public function show($id)
	{
		$comment = \App\Models\Comment::findOrFail($id);
		$commentTreeBuilder = new \App\Managers\TreeBuilder\CommentTreeItem();
		
		return \response()->json(['data' => $commentTreeBuilder->hideDepth()->getItem($comment->toArray())]);
	}
}
```

Example json results:

```json
{
"data": [
	 {
		"id": 50,
		"name": "Larson, Veum and Ondricka",
		"created_at": "2017-12-23T11:49:02+00:00",
		"user": {
			"id": 7,
			"name": "Brandyn Abbott DVM"
		},
		"children": [
			{
				"id": 73,
				"name": "Connelly-Zulauf",
				"created_at": "2017-12-23T11:49:11+00:00",
				"user": {
					"id": 9,
					"name": "Reba Sporer"
				},
				"children": [
					{
						"id": 85,
						"name": "Ivanov Ivan",
						"created_at": "2017-12-23T19:15:08+00:00",
						"user": {
							"id": 2,
							"name": "Ned Kunde"
						},
						"children": [],
						"depth": 3
					}
				],
				"depth": 2
			}
		],
		"depth": 1
	},
	{
		"id": 54,
		"name": "Gorczany-Swift",
		"created_at": "2017-12-23T11:49:02+00:00",
		"user": {
			"id": 11,
			"name": "Kevin Legros"
		},
		"children": [],
		"depth": 1
	}
        ]
}
```
