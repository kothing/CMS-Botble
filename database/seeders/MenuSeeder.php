<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Blog\Models\Category;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Facades\Menu;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Support\Arr;

class MenuSeeder extends BaseSeeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Main menu',
                'slug' => 'main-menu',
                'location' => 'main-menu',
                'items' => [
                    [
                        'title' => 'Home',
                        'url' => '/',
                    ],
                    [
                        'title' => 'Purchase',
                        'url' => 'https://botble.com/go/download-cms',
                        'target' => '_blank',
                    ],
                    [
                        'title' => 'Blog',
                        'reference_id' => Page::query()->skip(1)->value('id'),
                        'reference_type' => Page::class,
                    ],
                    [
                        'title' => 'Galleries',
                        'reference_id' => Page::query()->skip(4)->value('id'),
                        'reference_type' => Page::class,
                    ],
                    [
                        'title' => 'Contact',
                        'reference_id' => Page::query()->skip(2)->value('id'),
                        'reference_type' => Page::class,
                    ],
                ],
            ],

            [
                'name' => 'Favorite websites',
                'slug' => 'favorite-websites',
                'items' => [
                    [
                        'title' => 'Speckyboy Magazine',
                        'url' => 'https://speckyboy.com',
                    ],
                    [
                        'title' => 'Tympanus-Codrops',
                        'url' => 'https://tympanus.com',
                    ],
                    [
                        'title' => 'Kipalog Blog',
                        'url' => '#',
                    ],
                    [
                        'title' => 'SitePoint',
                        'url' => 'https://www.sitepoint.com',
                    ],
                    [
                        'title' => 'CreativeBlog',
                        'url' => 'https://www.creativebloq.com',
                    ],
                    [
                        'title' => 'TechTalk',
                        'url' => 'https://techtalk.vn',
                    ],
                ],
            ],

            [
                'name' => 'My links',
                'slug' => 'my-links',
                'items' => [
                    [
                        'title' => 'Homepage',
                        'url' => '/',
                    ],
                    [
                        'title' => 'Contact',
                        'reference_id' => Page::query()->skip(2)->value('id'),
                        'reference_type' => Page::class,
                    ],
                    [
                        'title' => 'Hotel',
                        'reference_id' => Category::query()->skip(5)->value('id'),
                        'reference_type' => Category::class,
                    ],
                    [
                        'title' => 'Travel Tips',
                        'reference_id' => Category::query()->skip(2)->value('id'),
                        'reference_type' => Category::class,
                    ],
                    [
                        'title' => 'Galleries',
                        'reference_id' => Page::query()->skip(4)->value('id'),
                        'reference_type' => Page::class,
                    ],
                ],
            ],

            [
                'name' => 'Featured Categories',
                'slug' => 'featured-categories',
                'items' => [
                    [
                        'title' => 'Lifestyle',
                        'reference_id' => Category::query()->skip(1)->value('id'),
                        'reference_type' => Category::class,
                    ],
                    [
                        'title' => 'Travel Tips',
                        'reference_id' => Category::query()->skip(2)->value('id'),
                        'reference_type' => Category::class,
                    ],
                    [
                        'title' => 'Healthy',
                        'reference_id' => Category::query()->skip(3)->value('id'),
                        'reference_type' => Category::class,
                    ],
                    [
                        'title' => 'Hotel',
                        'reference_id' => Category::query()->skip(5)->value('id'),
                        'reference_type' => Category::class,
                    ],
                    [
                        'title' => 'Nature',
                        'reference_id' => Category::query()->skip(6)->value('id'),
                        'reference_type' => Category::class,
                    ],
                ],
            ],

            [
                'name' => 'Social',
                'slug' => 'social',
                'items' => [
                    [
                        'title' => 'Facebook',
                        'url' => 'https://facebook.com',
                        'icon_font' => 'fab fa-facebook',
                        'target' => '_blank',
                    ],
                    [
                        'title' => 'Twitter',
                        'url' => 'https://twitter.com',
                        'icon_font' => 'fab fa-twitter',
                        'target' => '_blank',
                    ],
                    [
                        'title' => 'GitHub',
                        'url' => 'https://github.com',
                        'icon_font' => 'fab fa-github',
                        'target' => '_blank',
                    ],

                    [
                        'title' => 'Linkedin',
                        'url' => 'https://linkedin.com',
                        'icon_font' => 'fab fa-linkedin',
                        'target' => '_blank',
                    ],
                ],
            ],
        ];

        MenuModel::query()->truncate();
        MenuLocation::query()->truncate();
        MenuNode::query()->truncate();

        foreach ($data as $index => $item) {
            $menu = MenuModel::query()->create(Arr::except($item, ['items', 'location']));

            if (isset($item['location'])) {
                $menuLocation = MenuLocation::query()->create([
                    'menu_id' => $menu->id,
                    'location' => $item['location'],
                ]);

                LanguageMeta::saveMetaData($menuLocation);
            }

            foreach ($item['items'] as $menuNode) {
                $this->createMenuNode($index, $menuNode, $menu->id);
            }

            LanguageMeta::saveMetaData($menu);
        }

        Menu::clearCacheMenuItems();
    }

    protected function createMenuNode(int $index, array $menuNode, int|string $menuId, int|string $parentId = 0): void
    {
        $menuNode['menu_id'] = $menuId;
        $menuNode['parent_id'] = $parentId;

        if (isset($menuNode['url'])) {
            $menuNode['url'] = str_replace(url(''), '', $menuNode['url']);
        }

        if (Arr::has($menuNode, 'children')) {
            $children = $menuNode['children'];
            $menuNode['has_child'] = true;

            unset($menuNode['children']);
        } else {
            $children = [];
            $menuNode['has_child'] = false;
        }

        $createdNode = MenuNode::query()->create($menuNode);

        if ($children) {
            foreach ($children as $child) {
                $this->createMenuNode($index, $child, $menuId, $createdNode->id);
            }
        }
    }
}
