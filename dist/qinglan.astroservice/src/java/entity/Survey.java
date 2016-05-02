/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.codehaus.jackson.annotate.JsonIgnore;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "survey")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "Survey.findAll", query = "SELECT s FROM Survey s"),
    @NamedQuery(name = "Survey.findBySurveyId", query = "SELECT s FROM Survey s WHERE s.surveyId = :surveyId"),
    @NamedQuery(name = "Survey.findBySurveyName", query = "SELECT s FROM Survey s WHERE s.surveyName = :surveyName")})
public class Survey implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "survey_id")
    private Long surveyId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 20)
    @Column(name = "survey_name")
    private String surveyName;
    @Lob
    @Size(max = 65535)
    @Column(name = "survey_url")
    private String surveyUrl;
    @Lob
    @Size(max = 65535)
    @Column(name = "survey_description")
    private String surveyDescription;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "surveyId")
    private Collection<ObjectInfo> objectInfoCollection;

    public Survey() {
    }

    public Survey(Long surveyId) {
        this.surveyId = surveyId;
    }

    public Survey(Long surveyId, String surveyName) {
        this.surveyId = surveyId;
        this.surveyName = surveyName;
    }

    public Long getSurveyId() {
        return surveyId;
    }

    public void setSurveyId(Long surveyId) {
        this.surveyId = surveyId;
    }

    public String getSurveyName() {
        return surveyName;
    }

    public void setSurveyName(String surveyName) {
        this.surveyName = surveyName;
    }

    public String getSurveyUrl() {
        return surveyUrl;
    }

    public void setSurveyUrl(String surveyUrl) {
        this.surveyUrl = surveyUrl;
    }

    public String getSurveyDescription() {
        return surveyDescription;
    }

    public void setSurveyDescription(String surveyDescription) {
        this.surveyDescription = surveyDescription;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<ObjectInfo> getObjectInfoCollection() {
        return objectInfoCollection;
    }

    public void setObjectInfoCollection(Collection<ObjectInfo> objectInfoCollection) {
        this.objectInfoCollection = objectInfoCollection;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (surveyId != null ? surveyId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof Survey)) {
            return false;
        }
        Survey other = (Survey) object;
        if ((this.surveyId == null && other.surveyId != null) || (this.surveyId != null && !this.surveyId.equals(other.surveyId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.Survey[ surveyId=" + surveyId + " ]";
    }
    
}
