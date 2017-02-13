<?php

namespace AppBundle\Command;

use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\Snapshot\Snapshot;
use Prooph\EventStore\Snapshot\SnapshotStore;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserIdentity\Domain\Model\Publisher;
use UserIdentity\Domain\Model\PublisherId;

class TakeSnapshotCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:take_snapshot')
            ->setDescription('Generate snapshot')
            ->addArgument('publisherId', InputArgument::REQUIRED, 'uuid publisher');
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publisherId = PublisherId::fromString($input->getArgument('publisherId'));

        $publisher = $this->getContainer()->get('publisher_repository')->publisherOfId($publisherId);

        if (null === $publisher) {
            $output->writeln('Publisher not found');
            exit(1);
        }

        /** @var $snapshotStore SnapshotStore */
        $snapshotStore = $this->getContainer()->get('prooph_test.bundle.event_store.snapshotter');
        $snapshot = new Snapshot(
            AggregateType::fromAggregateRoot($publisher),
            $publisherId->toString(),
            $publisher,
            $this->getPublisherVersion($publisher),
            new \DateTimeImmutable("now", new \DateTimeZone('UTC'))
        );
        $snapshotStore->save($snapshot);

        $output->writeln('<info>Snapshot effectué</info>');
    }

    private function getPublisherVersion(Publisher $publisher)
    {
        $todoReflected = new \ReflectionClass($publisher);
        $versionProp = $todoReflected->getProperty('version');
        $versionProp->setAccessible(true);
        return $versionProp->getValue($publisher);
    }
}
