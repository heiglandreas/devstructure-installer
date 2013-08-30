<?php
/**
 * Copyright (c)2013-2013 heiglandreas
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIBILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2013-2013 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     27.08.13
 * @link      http://dev.mdv.wdv.de/gitlab/mdv/devstructure
 */

namespace Org_Heigl\Devstructure\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;


/**
 * Install this package.
 *
 * This does nothing else than to create a lot of directories and copy some
 * files
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2013-2013 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     27.08.13
 * @link      http://dev.mdv.wdv.de/gitlab/mdv/devstructure
 */
class Installer extends LibraryInstaller implements InstallerInterface
{
    /**
     * Path to the folder containing the directory-template
     *
     * @var string $templatePath
     */
    protected $templatePath = 'template';

    /**
     * Checks that provided package is installed.
     *
     * @param InstalledRepositoryInterface $repo    repository in which to check
     * @param PackageInterface             $package package instance
     *
     * @return bool
     */
//    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
//    {
//        return false;
//    }

    public function supports($packageType)
    {
        return 'org-heigl-devstructure' === $packageType;
    }


    /**
     * Installs specific package.
     *
     * @param InstalledRepositoryInterface $repo    repository in which to check
     * @param PackageInterface             $package package instance
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);
       return $this->doSomeWork($repo, $package);
    }

    /**
     * Updates specific package.
     *
     * Folders from the template-folder are created and files from the template-
     * folder are copied to the correct location. Files are <strong>not</strong>
     * overwritten except files containing <i>.dist</i> in the name.
     *
     * @param InstalledRepositoryInterface $repo    repository in which to check
     * @param PackageInterface             $initial already installed package version
     * @param PackageInterface             $target  updated version
     *
     * @throws InvalidArgumentException if $initial package is not installed
     */
//    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
//    {
//        return $this->doSomeWork($repo, $target);
//    }

    protected function doSomeWork(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $umask = umask(0000);
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->getTemplatePath($package)));
        foreach ($iterator as $item) {
            if ('.' === $item->getFilename() || '..' === $item->getFileName()) {
                continue;
            }
            $folder = $this->getMyTargetPath($package, $item);
            if (file_exists($folder) && false == strpos('.dist', $item->getFileName())) {
                continue;
            }
            if ($item->isDir()) {
                @mkdir($folder, 0000);
            } else {
                @mkdir(dirname($folder),0777, true);
                @copy($item->getPathName(), $folder);
            }
        }
        umask($umask);

        return $this;
    }

    /**
     * Uninstalls specific package.
     *
     * @param InstalledRepositoryInterface $repo    repository in which to check
     * @param PackageInterface             $package package instance
     */
//    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
//    {
//        //
//    }

    /**
     * Get the target path of an SPLFile-Object
     *
     * @param PackageInterface $package
     * @param SPLFile          $file
     *
     * @return string
     */
    protected function getMyTargetPath(PackageInterface $package, \SplFileInfo $file)
    {

        $templatePath = $this->getTemplatePath($package);

        $relativeFilePath = substr($file->getPathname(), strlen($templatePath)+1);

        return getcwd() . DIRECTORY_SEPARATOR . $relativeFilePath;
    }

    protected function getTemplatePath($package)
    {
        $path = $this->getPackageBasePAth($package)
              . DIRECTORY_SEPARATOR
              . $this->templatePath;

        return $path;
    }

//    public function getInstallPath(packageInterface $package)
//    {
//        return 'templates';
//    }
}