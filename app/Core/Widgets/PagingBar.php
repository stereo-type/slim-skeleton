<?php

namespace App\Core\Widgets;

use GuzzleHttp\Psr7\Query;
use Psr\Http\Message\UriInterface;
use Slim\Views\Twig;
use stdClass;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PagingBar
{

    public const PAGINBAR_TEMPLATE = 'catalog/paginbar.twig';
    /**
     * @var int The maximum number of pagelinks to display.
     */
    public int $maxdisplay = 18;

    /**
     * @var string|null A HTML link representing the "previous" page.
     */
    public ?string $previouslink = null;

    /**
     * @var string|null A HTML link representing the "next" page.
     */
    public ?string $nextlink = null;

    /**
     * @var string|null A HTML link representing the first page.
     */
    public ?string $firstlink = null;

    /**
     * @var string|null A HTML link representing the last page.
     */
    public ?string $lastlink = null;

    /**
     * @var array An array of strings. One of them is just a string: the current page
     */
    public array $pagelinks = [];

    private string $pagevar;

    /**
     * Constructor paging_bar with only the required params.
     *
     * @param int $totalcount The total number of entries available to be paged through
     * @param int $page The page you are currently viewing
     * @param int $perpage The number of entries that should be shown per page
     * @param UriInterface $baseurl url of the current page, the $pagevar parameter is added
     */
    public function __construct(
        public int $totalcount,
        public int $page,
        public int $perpage,
        public UriInterface $baseurl
    ) {
        $this->pagevar = 'page';
    }


    /**
     * @param Twig $twig
     * @param string $template
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(Twig $twig, string $template = self::PAGINBAR_TEMPLATE): string
    {
        return $twig->fetch($template, (array)$this->export_for_template());
    }

    /**
     * Export for template.
     *
     * @return stdClass
     */
    public function export_for_template(): stdClass
    {
        $data = new stdClass();
        $data->previous = null;
        $data->next = null;
        $data->first = null;
        $data->last = null;
        $data->label = 'Page';
        $data->pages = [];
        $data->haspages = $this->totalcount > $this->perpage;
        $data->pagesize = $this->perpage;

        if (!$data->haspages) {
            return $data;
        }

        if ($this->page > 0) {
            $data->previous = [
                'page' => $this->page,
                'url' => $this->baseurl->withQuery(Query::build([$this->pagevar => $this->page]))

            ];
        }

        $currpage = 0;
        if ($this->page > round(($this->maxdisplay / 3) * 2)) {
            $currpage = $this->page - round($this->maxdisplay / 3);
            $data->first = [
                'page' => 1,
                'url' => $this->baseurl->withQuery(Query::build([$this->pagevar => 0]))
            ];
        }

        $lastpage = 1;
        if ($this->perpage > 0) {
            $lastpage = ceil($this->totalcount / $this->perpage);
        }

        $displaycount = 0;
        $displaypage = 0;
        while ($displaycount < $this->maxdisplay and $currpage < $lastpage) {
            $displaypage = $currpage + 1;

            $iscurrent = $this->page == $currpage;
            $link = $this->baseurl->withQuery(Query::build([$this->pagevar => $currpage + 1]));

            $data->pages[] = [
                'page' => $displaypage,
                'active' => $iscurrent,
                'url' => $iscurrent ? null : $link
            ];

            $displaycount++;
            $currpage++;
        }

        if ($this->page + 1 != $lastpage) {
            $data->next = [
                'page' => $this->page,
                'url' => $this->baseurl->withQuery(Query::build([$this->pagevar => $this->page + 2 ])),
            ];
        }

        return $data;
    }

}