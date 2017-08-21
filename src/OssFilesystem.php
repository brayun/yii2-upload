namespace brayun\flysystem;

use creocoder\flysystem\Filesystem;
use Xxtime\Flysystem\Aliyun\OssAdapter;

/**
 * OssFilesystem
 * @author Ethan <ethan@brayun.com>
 */
class OssFilesystem extends Filesystem
{
    /**
     * @var string
     */
    public $internal = false;
    /**
     * @var string
     */
    public $ossServer = 'oss-cn-shanghai.aliyuncs.com';
    /**
     * @var string
     */
    public $ossServerInternal = 'oss-cn-shanghai-internal.aliyuncs.com';
    /**
     * @var string
     */
    public $accessKeyId;
    /**
     * @var string
     */
    public $accessKeySecret;
    /**
     * @var string
     */
    public $bucket;
    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->accessKeyId === null) {
            throw new InvalidConfigException('The "accessKeyId" property must be set.');
        }
        if ($this->accessKeySecret === null) {
            throw new InvalidConfigException('The "accessKeySecret" property must be set.');
        }
        if ($this->ossServer === null) {
            throw new InvalidConfigException('The "ossServer" property must be set.');
        }
        if ($this->bucket === null) {
            throw new InvalidConfigException('The "bucket" property must be set.');
        }
        if ($this->internal === true) {
            if ($this->ossServerInternal === null) {
                throw new InvalidConfigException('The "ossServerInternal" property must be set.');
            }
            $this->ossServer = $this->ossServerInternal;
        }
        parent::init();
    }
    /**
     * @return DropboxAdapter
     */
    protected function prepareAdapter()
    {
        return new OssAdapter([
          'access_id'     => $this->accessKeyId,
          'access_secret' => $this->accessKeySecret,
          'bucket'        => $this->bucket,
          'endpoint'       => $this->ossServers,
        ]);
    }
}
