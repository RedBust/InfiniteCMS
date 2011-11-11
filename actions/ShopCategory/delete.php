<?php
$table = ShopCategoryTable::getInstance();
$router->codeUnless(404, $category = $table->createQuery('c')
					->leftJoin('c.Items i')
					->where('c.id = ?', $id = $router->requestVar('id'))
					->fetchOne());

echo tag('h3', $category->getName());

if ($category->Items->count())
{
	$moveTo = $router->postVar('move');
	if ($moveTo && ($moveTo == -1 || $newCategory = $table->find($moveTo)))
	{
		if ($moveTo == -1)
		{
			Query::create()
				->delete('ShopItem')
				->where('category_id = ?', $category->id)
				->execute();
		}
		else
		{
			Query::create()
				->update('ShopItem')
				->set('category_id', $newCategory->id)
				->where('category_id = ?', $category->id)
				->execute();
		}
		$category->delete();
		redirect($table);
	}
	else //@todo what to do if it's the only category remaining ?
		echo make_form(array(
			array('move', lang('shop.cat.move_items_to'), 'record', array('model' => 'ShopCategory', 'empty' => true, 'exclude' => $category->id), $router->requestVar('move')),
		));
}
else
{
	$category->delete();
	redirect($table);	
}