<?php
namespace App\Service;

use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;


class Pagination {

    private $entityClass;
    private $limit=10;
    private $currentPage=1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(ObjectManager $manager,Environment $twig,RequestStack $request,$templatePath){

        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        // dump($this->route);
        // die();
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;

    }

    public function display(){

        // appel du moteur twig + template qu'on veut utiliser

        $this->twig->display($this->templatePath,[

            //options pour l'afficahge des données
            // (variables: route / page / pages)
            'page'=>$this->currentPage,
            'pages'=>$this->getPages(),
            'route'=>$this->route
        ]);
    }

    public function setEntityClass($entityClass){

        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass(){

        return $this->entityClass;
    }
    
    public function getLimit(){
        return $this->limit;
    }

    public function setLimit($limit){

        $this->limit=$limit;

        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }

    public function setPage($page){
        $this->currentPage=$page;
        return $this;
    }

    public function getData(){

        if(empty($this->entityClass)){

            throw new \Exception("setEntityClass n'a pas été renseigné dans le controller correspondant");
        }

        $offset = $this->currentPage * $this->limit - $this->limit;

        $repo = $this->manager->getRepository($this->entityClass);

        $data = $repo->findBy([],[],$this->limit,$offset);

        return $data;
    }

    public function getPages(){

        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        $pages = ceil($total/$this->limit);

        return $pages;
    }

    public function getRoute(){
        return $this->route;
    }

    public function setRoute($route){

        $this->route=$route;
        return $this;
    }

    public function getTemplatePath(){

        return $this->templatePath;
    }

    public function setTemplatePath($templatePath){
        $this->templatePath = $templatePath;
        return $this;
    }
}