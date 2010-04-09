class SuitesController < ApplicationController
  # GET /suites
  # GET /suites.xml
  def index
    @suites = Suite.all

    respond_to do |format|
      format.html # index.html.erb
      format.xml  { render :xml => @suites }
    end
  end

  # GET /suites/1
  # GET /suites/1.xml
  def show
    @suite = Suite.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.xml  { render :xml => @suite }
    end
  end

  # GET /suites/new
  # GET /suites/new.xml
  def new
    @suite = Suite.new

    respond_to do |format|
      format.html # new.html.erb
      format.xml  { render :xml => @suite }
    end
  end

  # GET /suites/1/edit
  def edit
    @suite = Suite.find(params[:id])
  end

  # POST /suites
  # POST /suites.xml
  def create
    @suite = Suite.new(params[:suite])
    vulnerabilities = Vulnerability.find(:all, :conditions => {:published => @suite.start..@suite.end } )
    #weaknesses is a hash mapping weaknesses to the vulnerabilities they map to, ie Sequel Injection => [a, b, c...]
    weaknesses = {} 
    averages = {}
    vulnerabilities.each do |vulnerability|
      if weaknesses.has_key? vulnerability.weakness.id
        weaknesses[vulnerability.weakness.id].push vulnerability
        #updated averages
        averages[vulnerability.weakness.id] += vulnerability.score
      else
        weaknesses[vulnerability.weakness.id] = [vulnerability]
        averages[vulnerability.weakness.id] = vulnerability.score
        #set initial average...
      end
    end
    averages.each { |k, v| averages[k] = v/weaknesses[k].size }
    
    #find ranks
    #in array, insert all vulnerability/count pairs, sort by pairs.
    ranks = []
    weaknesses.each do |weakness, vulnerabilities|
      count = vulnerabilities.length
      ranks.push [weakness, count]
    end
    
    ranks.sort! {|a, b| a[1] <=> b[1]}


    rank_multiplier = 10.0/ranks.length
    
    ranks.each_with_index do |rank, index|
      weaknesses[rank[0]] = (index + 1) * rank_multiplier
    end
    
    ranks.clear
    
    weaknesses.each do |weakness, rank|
      score = rank * @suite.weight + averages[weakness] * (1.0 - @suite.weight)
      logger.info "#{weakness}: #{averages[weakness]}, #{rank}, #{score}";
      ranks.push [weakness, score]
    end
    
    #now sort, choose top weaknesses...
    ranks.sort! {|a,b| b[1] <=> a[1]} #sorting highest to lowest.
    ranks[0...@suite.top].each do |top_rated|
      @suite.tests.build( :weakness_id => top_rated[0] )
    end
        
    respond_to do |format|
      if @suite.save
        flash[:notice] = 'Suite was successfully created.'
        format.html { redirect_to(@suite) }
        format.xml  { render :xml => @suite, :status => :created, :location => @suite }
      else
        format.html { render :action => "new" }
        format.xml  { render :xml => @suite.errors, :status => :unprocessable_entity }
      end
    end
  end

  # PUT /suites/1
  # PUT /suites/1.xml
  def update
    @suite = Suite.find(params[:id])

    respond_to do |format|
      if @suite.update_attributes(params[:suite])
        flash[:notice] = 'Suite was successfully updated.'
        format.html { redirect_to(@suite) }
        format.xml  { head :ok }
      else
        format.html { render :action => "edit" }
        format.xml  { render :xml => @suite.errors, :status => :unprocessable_entity }
      end
    end
  end

  # DELETE /suites/1
  # DELETE /suites/1.xml
  def destroy
    @suite = Suite.find(params[:id])
    @suite.destroy

    respond_to do |format|
      format.html { redirect_to(suites_url) }
      format.xml  { head :ok }
    end
  end
end
