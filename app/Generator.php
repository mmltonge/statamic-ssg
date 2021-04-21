<?php


namespace App;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Statamic\Entries\EntryCollection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;

class Generator extends \Statamic\StaticSite\Generator
{
    protected $entry = null;
    protected $collection = null;

    public function __construct(Application $app, Filesystem $files, $collection = null, $entry = null)
    {
        parent::__construct($app, $files);
        $this->entry = $entry;
        $this->collection = $collection;
    }

    public function generate()
    {
        Site::setCurrent(Site::default()->handle());

        $this
            ->bindGlide()
            ->backupViewPaths()
            ->clearDirectory()
            ->createContentFiles()
            ->createSymlinks()
            ->copyFiles();

        $response[] = 'Static site generated into ' . $this->config['destination'];

        if ($this->skips) {
            $response[] = "[!] {$this->skips}/{$this->count} pages not generated";
        }

        if ($this->warnings) {
            $response[] = "[!] {$this->warnings}/{$this->count} pages generated with warnings";
        }

        if ($this->after) {
            call_user_func($this->after);
        }

        return $response;
    }

    public function clearDirectory()
    {
        $dir = $this->config['destination'];
        $ds = DIRECTORY_SEPARATOR;

        if(!is_null($this->entry)) {
            $dir .= $ds . $this->collection->id() . $ds . $this->entry->slug();
        } elseif(!is_null($this->collection)) {
            $dir .= $ds . $this->collection->id();
        }

        $this->files->deleteDirectory($dir, true);

        return $this;
    }

    protected function entries()
    {
        if(!is_null($this->entry->id())) { // Entry not null, we want to build a specific entry
            return EntryCollection::make()->add($this->createPage($this->entry));
        } elseif(!is_null($this->collection->id())) { // Or maybe everything inside a collection
            return Entry::all()
                ->reject(function ($entry) {
                    return is_null($entry->uri());
                })
                ->reject(function ($entry) {
                    return $this->collection->id() !== $entry->collection()->id();
                })
                ->map(function ($content) {
                    return $this->createPage($content);
                })
                ->filter
                ->isGeneratable();
        }

        // Otherwise, just all published content
        return Entry::all()
            ->reject(function ($entry) {
                return is_null($entry->uri());
            })
            ->map(function ($content) {
                return $this->createPage($content);
            })
            ->filter
            ->isGeneratable();
    }
}
