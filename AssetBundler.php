<?php
class AssetBundler {
	protected $learnings;
	protected $bundles;
	protected $options;
	
	public function __construct($options = array()) {
		$this->options = $options;
		$this->learnings = array();
	}
	
	/**
	 * Give the AssetBundler data that helps it determine which bundles to use
	 * @param array $files An array of files that have been requested together
	 * @param int $count The number of times those files have been requested together
	 */
	public function learn($files, $count=1) {
		$key = $this->generateKey($files);
		if(!isset($this->learnings[$key])) {
			$this->learnings[$key] = array(
				'files'=>$files,
				'count'=>0
			);
		}
		
		$this->learnings[$key]['count'] += $count;
	}
	
	/**
	 * Takes an array of files and returns a modified array that uses bundles where appropriate
	 * @param array $files An array of files being requested
	 * @return array A new array of files that uses bundles
	 */
	public function bundle($files) {
		$bundles = $this->bundles;
		
		$used_files = array();
		
		$return = array();
		
		foreach($files as $file) {
			if(isset($used_files[$file])) continue;
			$found = false;
			foreach($bundles as $k=>$bundle) {
				if(in_array($file,$bundle)) {
					$return[] = $k;
					foreach($bundle as $f) {
						$used_files[$f] = true;
					}
					unset($bundles[$k]);
					$found = true;
					break;;
				}
			}
			if(!$found) {
				$return[] = $file;
			}
		}
		
		return $return;
	}
	
	/**
	 * Gets the defined bundles
	 * @return array A list of bundles
	 */
	public function getBundles() {
		return $this->bundles;
	}
	
	/**
	 * Sets the bundles
	 * @param array $bundles An array of bundles
	 */
	public function setBundles($bundles) {
		$this->bundles = $bundles;
	}
	
	/**
	 * Gets all the data that has been learned
	 * @return array An array of learned data
	 */
	public function getLearnings() {
		return $this->learnings;
	}
	
	/**
	 * Sets the data that has been learned
	 * @param array $learnings An array of learned data
	 */
	public function setLearnings($learnings) {
		$this->learnings = $learnings;
	}
	
	/**
	 * Chooses bundles and generates files based on the learned data
	 */
	public function generateBundles() {
		$total_requests = 0;
		$sets = array();
		
		$this->bundles = array();
		
		if(!$this->learnings) return;
		
		foreach($this->learnings as $stat) {
			$total_requests += $stat['count'];
			$powerset = $this->powerSet($stat['files'],1);
			
			foreach($powerset as $set) {
				$key = (serialize($set));
				if(!isset($sets[$key])) {
					$sets[$key] = array(
						'set'=>$set,
						'count'=>$stat['count']
					);
				}
				else {
					$sets[$key]['count'] += $stat['count'];
				}
			}
		}
		
		usort($sets,function($a,$b) use($total_requests) {
			return count($b['set'])*$b['count'] - count($a['set'])*$a['count'];
		});
		
		$used = array();
		$bundles = array();
		foreach($sets as $k=>$set) {
			foreach($set['set'] as $asset) {
				if(isset($used[$asset])) {
					continue 2;
				}
			}
			$bundles[$this->getBundleFile($set['set'])] = $set['set'];
			foreach($set['set'] as $asset) {
				$used[$asset] = true;
			}
		}
		
		$this->bundles = $bundles;
	}
	
	
	
	
	protected function powerSet($in,$minLength = 1) {
		$count = count($in);
		$members = pow(2,$count);
		$return = array();
		for ($i = 0; $i < $members; $i++) {
			$b = sprintf("%0".$count."b",$i);
			$out = array();
			for ($j = 0; $j < $count; $j++) {
				if ($b{$j} == '1') $out[] = $in[$j];
			}
			if (count($out) >= $minLength) {
				$return[] = $out;
			}
		}
		return $return;
	}
	
	protected function getBundleFile($bundle) {
		//if there is only 1 file in the bundle, return the filename directly
		if(count($bundle) === 1) return current($bundle);
		
		$ext = array_pop(explode('.',current($bundle)));
		
		//$bundle = array_map(function($el) use($ext) { return basename($el,'.'.$ext); }, $bundle);
		//$filename = implode('__',$bundle).'.'.$ext;
		
		$filename = $this->generateKey($bundle).'.'.$ext;
		
		//TODO: if bundle file doesn't exist, create it
		
		return $filename;
	}
	
	protected function generateKey($files) {
		return md5(serialize($files));
	}
}
