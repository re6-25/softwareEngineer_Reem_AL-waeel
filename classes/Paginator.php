<?php
class Paginator {
    public $limit;
    public $page;
    public $offset;
    public function __construct($page=1, $limit=10) {
        $this->limit = max(1, intval($limit));
        $this->page = max(1, intval($page));
        $this->offset = ($this->page-1)*$this->limit;
    }

}
?>
