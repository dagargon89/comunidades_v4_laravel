<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;

class OrganigramManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Gestión de Organigrama';
    protected static ?string $title = 'Gestión de Organigrama';
    protected static string $view = 'filament.pages.organigram-manager';

    protected function getTableQuery()
    {
        return User::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->label('Nombre')->searchable(),
            TextColumn::make('email')->label('Correo')->searchable(),
            SelectColumn::make('parent_id')
                ->label('Jefe directo')
                ->options(fn () => User::pluck('name', 'id')->toArray())
                ->searchable()
                ->placeholder('Sin jefe')
                ->afterStateUpdated(function ($record, $state) {
                    if ($record->id == $state) return; // No permitir ser su propio jefe
                    $record->parent_id = $state ?: null;
                    $record->save();
                }),
        ];
    }

    public function getMermaidOrgChart(): string
    {
        $users = User::all();
        $lines = ["graph TD", 'Root["Sin jefe"]'];
        foreach ($users as $user) {
            $childLabel = "U{$user->id}[\"" . addslashes($user->name) . "\"]";
            if ($user->parent_id) {
                $parent = $users->find($user->parent_id);
                $parentLabel = $parent ? "U{$parent->id}[\"" . addslashes($parent->name) . "\"]" : 'Root["Sin jefe"]';
                $lines[] = "{$parentLabel} --> {$childLabel}";
            } else {
                $lines[] = "Root --> {$childLabel}";
            }
        }
        return implode("\n", array_unique($lines));
    }

    public function getOrgTreeHtml(): string
    {
        $users = \App\Models\User::all();
        $tree = $users->groupBy('parent_id');
        $renderTree = function ($parentId) use (&$renderTree, $tree) {
            if (empty($tree[$parentId])) return '';
            $html = '<ul class="ml-6 border-l-2 border-gray-200">';
            foreach ($tree[$parentId] as $user) {
                $html .= '<li class="my-1 pl-2">' . e($user->name) . $renderTree($user->id) . '</li>';
            }
            $html .= '</ul>';
            return $html;
        };
        return $renderTree(null);
    }

    public function getOrgTreeTreant(): string
    {
        $users = \App\Models\User::all();
        $tree = $users->groupBy('parent_id');
        $buildTree = function ($parentId) use (&$buildTree, $tree) {
            $children = [];
            foreach ($tree[$parentId] ?? [] as $user) {
                $children[] = [
                    'text' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'children' => $buildTree($user->id),
                ];
            }
            return $children;
        };
        $root = [
            'text' => ['name' => 'Sin jefe'],
            'children' => $buildTree(null),
        ];
        return json_encode($root);
    }

    public function getOrgChartData(): string
    {
        $users = \App\Models\User::all();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'pid' => $user->parent_id,
            ];
        }
        // Nodo raíz ficticio si hay usuarios sin jefe
        if (collect($data)->whereNull('pid')->count() > 1) {
            $data[] = [
                'id' => 'root',
                'name' => 'Sin jefe',
            ];
            foreach ($data as &$item) {
                if (empty($item['pid'])) {
                    $item['pid'] = 'root';
                }
            }
        }
        return json_encode($data);
    }

    public function getVanillaTreeHtml(): string
    {
        $users = \App\Models\User::all();
        $usersById = $users->keyBy('id');
        $tree = $users->groupBy('parent_id');
        $renderTree = function ($parentId) use (&$renderTree, $tree, $usersById) {
            if (empty($tree[$parentId])) return '';
            $html = '<ul>';
            foreach ($tree[$parentId] as $user) {
                $html .= '<li>' . e($user->name) . $renderTree($user->id) . '</li>';
            }
            $html .= '</ul>';
            return $html;
        };
        // Si hay más de un usuario sin jefe, agrúpalos bajo un nodo raíz
        if (isset($tree[null]) && count($tree[null]) > 1) {
            $html = '<ul><li><b>Sin jefe</b>' . $renderTree(null) . '</li></ul>';
        } else {
            $html = $renderTree(null);
        }
        return $html;
    }
}
