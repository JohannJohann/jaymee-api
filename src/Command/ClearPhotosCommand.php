<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Image;
use App\Controller\ImageController;

class ClearPhotosCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:clear-photos';
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Remove all photos that are more than 24 hours old.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to remove all photos that are more than 24 hours old.')
    ;    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $imageRepository = $this->em->getRepository(Image::class);

        $expiredImages = $imageRepository->getExpired();
        foreach($expiredImages as $image){
            $url = getenv('PRIVATE_PHOTO_STORAGE').$image->getOwner()->getId().'/'.$image->getFilename();
            if(file_exists($url)){
                unlink($url);
            }
            $this->em->remove($image);
            $this->em->flush();
        }
        $output->write(count($expiredImages).' images supprimées.');
    }
}