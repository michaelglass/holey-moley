namespace :db do
  namespace :static do
    namespace :import_data do
      desc 'import nvd data into database'
      task 'nvd' => :environment do
        Crewait.start_waiting
        total_entries = 0
        usable_entries = 0
        timer_start = Time.now
        nvd_prefix = "static_data/nvd/nvdcve-2.0-20"
        nvd_suffix = ".xml"
        nvd_middles = ['02', '03', '04', '05', '06', '07', '08', '09', '10']
        
        nvd_middles.each do |year|
          node = XML::Parser.file(nvd_prefix + year + nvd_suffix).parse.child
          file_timer_start = Time.now
          node.each_element do |entry|
            total_entries += 1
            id = entry['id']
            id = id[4,4] + id[9,4]
            id = id.to_i
          
            published, modified, cwe_id, score = [-1,-1,-1,-1]

            entry.each_element do |node|
              case node.name
                when 'published-datetime'
                  published = DateTime.parse(node.content)
                when 'last-modified-datetime'
                  modified = DateTime.parse(node.content)
                when 'cwe'
                  cwe_id = node['id']
                  cwe_id.slice!(0,4)
                  cwe_id = cwe_id.to_i
                when 'cvss' #get score...
                  node.each_element do |cvss_properties|
                    if(cvss_properties.name == 'base_metrics')
                      cvss_properties.each_element do |base_metric|
                        score = base_metric.content if base_metric.name == 'score'
                      end
                    end
                  end
              end          
            
            
              break if(published != -1 && modified != -1 && cwe_id != -1 && score != -1)
            end #entry properties...
          
          
            next if(published == -1 || cwe_id == -1 || score == -1) #insufficient data
          
            modified = published if modified == -1
          
            usable_entries += 1
            Vulnerability.crewait(:id => id, :weakness_id => cwe_id, :score => score, :modified => modified, :published => published)
          end #entries
          puts "done with #{year} in #{Time.now - file_timer_start} seconds (#{Time.now - timer_start} elapsed)" 
        end #all files
        puts "We used #{usable_entries} entries out of a total of #{total_entries} entries in the input file.  That's #{(usable_entries.to_f / total_entries.to_f)*100}%" 
        Crewait.go!
      end
    end
  end
end