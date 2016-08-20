<?php
namespace installer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use GuzzleHttp\ClientInterface;
use ZipArchive;
class NewInstaller extends Command{

    private $client;
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        parent::__construct();
    }
    //method used to apply any setters or accept some arguments
    public function configure()
    {
        $this->setName('new')
            ->setDescription('Builds A New Elham Application For You')
            ->addArgument('name',InputArgument::REQUIRED);
    }
    //method used to process the commands however we need to
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (! class_exists('ZipArchive'))
            throw new RuntimeException('The Zip PHP extension is not installed. Please install it and try again.');

        //assert a directory exists or not
        $directory = getcwd().'/'.$input->getArgument('name');
        $output->writeln('<info>Crafting Elham App....</info>');
        $this->check($directory,$output);
        //download the nightly version of Elham
        $this->download($zipFile = $this->makeFileName())
             ->extract($zipFile,$directory)
             ->cleanUp($zipFile);

        //extract the .zip file
        //alert the user that Elham application is ready to go
        $output->writeln('<comment>Elham app is ready to use</comment>');
    }

    private function check($directory,OutputInterface $output)
    {
        if(is_dir($directory))
        {
            $output->writeln('<error>Application already exists</error>');
            exit(1);
        }
    }

    private function download($zipFile)
    {
        //https://github.com/chandan07cse/Elham/releases/latest
        //https://github.com/chandan07cse/Elham/archive/v1.0.0.zip
        $response = $this->client->get('https://github.com/chandan07cse/Elham/archive/v1.0.0.zip')->getBody();
        file_put_contents($zipFile,$response);
        return $this; //for continue chaining methods
    }
    private function makeFileName(){
        return getcwd().'/elham_'.md5(time().uniqid()).'.zip';
    }

    private function extract($zipFile,$directory)
    {
        $archive = new ZipArchive;
        $archive->open($zipFile);
        $archive->extractTo($directory);
        $archive->close();
        return $this; // just in case if we need to continue chaining
    }

    private function cleanUp($zipFile)
    {
        @chmod($zipFile,0777);
        @unlink($zipFile);
        return $this;
    }
}