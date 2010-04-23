#http://static.nvd.nist.gov/feeds/xml/cve/nvdcve-2.0-modified.xml
require 'net/http'
require 'uri'
namespace :db do
  namespace :static do
    namespace :import_data do
      desc 'update nvd data in database'
      task 'update_nvd' => :environment do
        Crewait.start_waiting
        total_entries = 0
        usable_entries = 0
        timer_start = Time.now
        
        nvd_url = URI.parse("http://static.nvd.nist.gov/feeds/xml/cve/nvdcve-2.0-modified.xml")
        file_path = "static_data/nvd/nvdcve-2.0-modified-#{timer_start.to_i}.xml"
        File.open(file_path, 'w') do |f|  
          res = Net::HTTP.start(nvd_url.host) do |http|
            http.get(nvd_url.path)
          end
          f.write res.body
        end
        
        
        node = XML::Parser.file(file_path).parse.child
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
                published = DateTime.parse(node.content).utc
              when 'last-modified-datetime'
                modified = DateTime.parse(node.content).utc
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
          if(Vulnerability.exists?(id))
            vuln = Vulnerability.find(id)
            changes = []
            if vuln.weakness_id != cwe_id
              changes << "weakness" 
              vuln.weakness_id = cwe_id
            end
            if vuln.score != BigDecimal(score)
              changes << "score"
              vuln.score = score
            end
            if vuln.modified.to_datetime.to_s != modified.to_s #HACK!!! TO_S WORKS BETTER THAN REGULAR COMPARISON
              changes << "modified"
              vuln.modified = modified
            end
            if vuln.published.to_datetime.to_s != published.to_s
              changes << "published" 
              vuln.published = published
            end
            
            if changes.empty?
              #no changes...
              # puts "#{id} unchanged"
              usable_entries -= 1
            else
              # puts "#{id} changes: #{changes.join ", "}"
              # puts "#{vuln.published.to_datetime}(#{vuln.published.class}) <=> #{published}(#{published.class}), #{vuln.score}(#{vuln.score.class}) <=> #{score}(#{score.class})"
                 
              vuln.save
            end
          else
            Vulnerability.crewait(:id => id, :weakness_id => cwe_id, :score => score, :modified => modified, :published => published)
          end
        end #entriesan
        puts "done in #{Time.now - file_timer_start} seconds (#{Time.now - timer_start} elapsed)" 
        puts "We used #{usable_entries} entries out of a total of #{total_entries} entries in the input file.  That's #{(usable_entries.to_f / total_entries.to_f)*100}%" 
        Crewait.go!
      end
    end
  end
end