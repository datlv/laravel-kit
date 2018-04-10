<?php

namespace Datlv\Kit\Traits\Presenter;

/**
 * Class NestablePresenter
 *
 * @package Datlv\Kit\Traits\Presenter
 * @property-read mixed $entity
 */
trait NestablePresenter {
    /**
     * Lấy nodes con cháu của $root
     * Hàm $fn dùng xử lý $query trước khi get(), vd: load translation attributes...
     *
     * @param \Datlv\Kit\Extensions\NestedSetModel $root
     * @param bool $self
     * @param \Closure $fn
     *
     * @return \Datlv\Kit\Extensions\NestedSetModel[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function getDescendantNestedSet( $root, $self = false, $fn = null ) {
        $query = $self ? $root->descendantsAndSelf() : $root->descendants();
        if ( $fn ) {
            $query = $fn( $query );
        }

        return $query->get();
    }

    /**
     * Render html theo định dạng của jquery nestable plugin
     *
     * @see https://github.com/dbushell/Nestable
     *
     * @param \Datlv\Kit\Extensions\NestedSetModel $root
     * @param int $max_depth
     * @param bool $self
     * @param null|\Closure $fn
     * @param string $route_prefix
     *
     * @return string
     */
    protected function toNestable( $root, $max_depth, $self = false, $fn = null, $route_prefix = '' ) {
        $nodes = $this->getDescendantNestedSet( $root, $self, $fn );
        $html = '';
        $depth = $root->depth;
        foreach ( $nodes as $node ) {
            // end current node
            if ( $node->depth < $depth ) {
                $html .= str_repeat( '</ol></li>', $depth - $node->depth );
            }

            // start new node
            $html .= <<<"LIST_ITEM"
<li class="dd-item nested-list-item" data-id="{$node->id}">
    <div class="dd-handle nested-list-handle"><span class="glyphicon glyphicon-align-justify"></span></div>
    <div class="nested-list-content">
        {$node->present()->label}
        <div class="nested-list-actions pull-right">
            {$node->present()->actions( $max_depth, $route_prefix )}
        </div>
    </div>
LIST_ITEM;
            // end node or open sub nodes
            $html .= $node->rgt - $node->lft == 1 ? '</li>' : '<ol class="dd-list">';

            $depth = $node->depth;
        }

        return $html ? "<ol class=\"dd-list\">$html</ol>" : null;
    }

    /**
     * Tạo data select tag theo định dạng selectize
     *
     * @param \Illuminate\Database\Eloquent\Collection|array $roots
     *
     * @return array
     */
    public function toSelectize( $roots ) {
        $result = [];
        foreach ( $roots as $node ) {
            $result = array_merge( $result, $this->selectizeNode( $node ) );
        }
        $this->selectizeAddInfo( $result );
        foreach ( $result as &$node ) {
            $node['attributes']['data-data'] = htmlentities( json_encode( $node['attributes']['data-data'] ) );
        }

        return $result;
    }

    /**
     * @param array $list
     */
    protected function selectizeAddInfo( &$list ) {
        if ( $count = count( $list ) ) {
            $list[0]['attributes']['data-data']['first'] = 1;
            $list[$count - 1]['attributes']['data-data']['last'] = 1;
        }
    }

    /**
     * @param \Datlv\Kit\Extensions\NestedSetModel|\Laracasts\Presenter\PresentableTrait $node
     * @param integer $level
     *
     * @return string
     */
    protected function selectizeNode( $node, $level = 1 ) {
        $result = [
            [
                'value'      => $node->id,
                'text'       => $node->present()->label,
                'attributes' => [ 'data-data' => [ 'level' => $level ] ],
            ],
        ];

        if ( ! $node->isLeaf() ) {
            $children = [];
            foreach ( $node->children as $child ) {
                $children = array_merge( $children, $this->selectizeNode( $child, $level + 1 ) );
            }
            $this->selectizeAddInfo( $children );
            $result = array_merge( $result, $children );
        }

        return $result;
    }

    /**
     * Tạo tree data cho bootstrap treeview
     * $max_depth Max chiều sâu theo $root->depth, vd: 1 ~ 1 cấp, các con trức tiếp của root
     *
     * @see https://github.com/jonmiles/bootstrap-treeview
     *
     * @param \Datlv\Kit\Extensions\NestedSetModel $root
     * @param \Datlv\Kit\Extensions\NestedSetModel|null $current
     * @param int $max_depth
     * @param bool $self
     * @param \Closure $fn
     *
     * @return string JSON format
     */
    protected function toTree( $root, $current = null, $max_depth = null, $self = false, $fn = null ) {
        $nodes = $this->getDescendantNestedSet( $root, $self, $fn );
        $max_depth = $max_depth ? $max_depth + $root->depth : 10000;
        $json = '';
        $depth = $root->depth;
        $current_id = $current ? $current->id : null;
        foreach ( $nodes as $i => $node ) {
            if ( $node->depth <= $max_depth ) {
                // end current node
                if ( $node->depth < $depth ) {
                    $json .= str_repeat( ']}', $depth - $node->depth );
                }
                // start new node
                $selected = $current_id === $node->id ? '"state":{"expanded":true,"selected":true},' : '';
                if ( $selected ) {
                    // Set thuộc tính expanded=true cho các nodes cha/ông của $node
                    $search = [];
                    $replace = [];
                    $j = $i - 1;
                    while ( ( $j >= 0 ) && ( $nodes[$j]->depth > $root->depth - ( $self ? 1 : 0 ) ) ) {
                        // $nodes[$j] là cha/ông của $node
                        if ( ( $nodes[$j]->lft <= $node->lft ) && ( $nodes[$j]->rgt >= $node->rgt ) ) {
                            $id = '"data-id":' . $nodes[$j]->id;
                            $search[] = $id;
                            $replace[] = "$id,\"state\":{\"expanded\":true}";
                        }
                        $j --;
                    }
                    if ( $search && $replace ) {
                        $json = str_replace( $search, $replace, $json );
                    }
                }
                $json .= ',{' . $selected . '"text":"' . $node->present()->label . '","data-slug":"'.$node->slug.'","data-id":' . $node->id;
                $json .= ( $node->rgt - $node->lft == 1 ) || ( $node->depth == $max_depth ) ? '}' : ',"nodes":[';
                $depth = $node->depth;
            }
        }

        if ( $root->depth < $depth - 1 ) {
            $json .= str_repeat( ']}', $depth - $root->depth - 1 );
        }

        return str_replace( '[,', '[', "[{$json}]" );
    }
}
