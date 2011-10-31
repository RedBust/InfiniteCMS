<?php
$table = ShopCategoryTable::getInstance();
$categories = $table->findAll();

if ($categories->count())
{
	echo '<ul>';
	foreach ($categories as $category)
	{
		echo tag('li', $category->getName() . $category->getUpdateLink() . $category->getDeleteLink());
	}
	echo '</ul>';
}

echo tag('br') . make_link(new ShopCategory);